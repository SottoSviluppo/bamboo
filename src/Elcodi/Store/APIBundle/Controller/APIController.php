<?php

namespace Elcodi\Store\APIBundle\Controller;

use Elcodi\Component\Currency\Entity\Money;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class APIController extends Controller
{
    public function loginAction(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $loginResponse = "";
        $eMail = "";
        $name = "";
        $surname = "";
        $message = "error";
        $response = "KO";
        $status = "invalid user login";

        try
        {
            $user = $this->getUserByEmail($email);

            if ($user === null) {
                throw new Exception("invalid user login", 1);
            }

            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);

            // Note the difference
            if (!$encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
                throw new Exception("invalid user login", 1);
            }

            $this->authenticate($request, $user, $password);

            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();

            $message = "success";
            $response = "OK";
            $status = "authenticated";

        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }

        return $this->getJson(array(
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'user_status' => [
                'message' => $message,
                'response' => $response,
                'status' => $status,
            ],
        )
        );
    }

    // funziona solo se non hai "remember me", pare
    public function logoutAction(Request $request)
    {
        try
        {
            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);

            $this->get('security.token_storage')->setToken(null);
            $this->get('request')->getSession()->invalidate();

            return $this->getSuccessMessage();
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function getUserDataAction(Request $request)
    {
        try
        {
            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);

            return $this->getJson([
                'message' => 'success',
                'status' => 'OK',
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                "birthdate" => $this->getDateString($customer->getBirthday()),
            ]);
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function setUserDataAction(Request $request)
    {
        try
        {
            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);

            $firstName = $request->get('first_name');
            $lastName = $request->get('last_name');
            $birthdate = $request->get('birthdate');

            if ($firstName != '') {
                $customer->setFirstName($firstName);
            }
            if ($lastName != '') {
                $customer->setLastName($lastName);
            }

            if ($birthdate != '') {
                $birthDay = \DateTime::createFromFormat('Y-m-d', $birthdate);
                if (!is_object($birthDay)) {
                    return $this->getFailedMessage('Date format not correct YYYY-MM-DD');
                }

                $customer->setBirthday($birthDay);
            }

            $this->get('elcodi.object_manager.customer')->persist($customer);
            $this->get('elcodi.object_manager.customer')->flush();

            return $this->getSuccessMessage();
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    // TODO RIPULIRE
    public function registerAction(Request $request)
    {
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');
        $email = $request->get('email');
        $password = $request->get('password');

        $message = '';
        $status = '';

        try
        {
            // check if mail already exists
            $user = $this->getUserByEmail($email);
            if ($user !== null) {
                throw new Exception("email already exist", 1);
            }

            $customer = $this
                ->get('elcodi.factory.customer')
                ->create()
                ->setPassword($password)
                ->setEmail($email)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setCompany(false)
                ->setEnabled(true);

            $customer->setSalt(uniqid(mt_rand()));

            $this->get('elcodi.object_manager.customer')->persist($customer);
            $this->get('elcodi.object_manager.customer')->flush();

            // login
            $this->authenticate($request, $customer, $password);
        } catch (Exception $exception) {
            // TODO se non è stessa email manda "generic error in registration method"
            return $this->getJson(
                array(
                    'Message' => 'error',
                    'Status' => $exception->getMessage(),
                )
            );
        }

        return $this->getJson(
            array(
                'Message' => "registration success",
                'Status' => "success",
            )
        );
    }

    public function getOrdersAction(Request $request)
    {
        try
        {
            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);

            $orders = $this->get('elcodi.manager.tonki')->getOrders($customer);
            $orderStates = $this->get('elcodi.manager.tonki')->getOrderStates($orders);

            $results = array();
            foreach ($orders as $order) {
                $orderState = $orderStates[$order->getId()];
                $orderPaymentMethodName = '';
                $shipping = array();

                if ($order->getPaymentMethod() != null) {
                    $orderPaymentMethodName = $order->getPaymentMethod()->getName();
                    $orderPaymentMethodName = str_replace('elcodi_plugin.', '', $orderPaymentMethodName);
                    $orderPaymentMethodName = str_replace('.name', '', $orderPaymentMethodName);
                    $shipping = ['type' => 'express'];
                }

                $shippingState = 'common.order.states.' . $order->getShippingStateLineStack()->getLastStateLine()->getName();

                $results[] = [
                    'id' => $order->getId(),
                    'date' => $this->getDateTimeString($order->getCreatedAt()),
                    'payment' => [
                        'status' => $order->getPaymentStateLineStack()->getLastStateLine()->getName(),
                        'method' => $orderPaymentMethodName,
                    ],
                    'shipping' => $shipping,
                    'shipping_state' => $this->get('translator')->trans($shippingState),
                    'address' => $this->getAddressData($order->getDeliveryAddress(), $customer),
                    'shipping_cost' => $this->getDecimalPriceFromPrice($order->getShippingAmount()),
                    'total' => $this->getDecimalPriceFromPrice($order->getAmount()),
                    'currency' => $order->getAmount()->getCurrency()->getIso(),
                    'details' => $details,
                ];
            }

            return $this->getJson([
                'orders' => $results,
            ]);
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function createPaypalOrderAction(Request $request)
    {
        try
        {
            $this->updateCart();

            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);
            $this->throwExceptionIfNoProductsInCart();
            $this->setDeliveryAddressToCart($customer);
            $this->createPaypalOrder();

            $order = $this->get('paymentsuite.bridge')->getOrder();

            $order = $this->get('elcodi.repository.order')->find($order->getId());
            $order->setExtraDataValue('order_made_with_app', '1');

            $this->get('elcodi.object_manager.order')->persist($order);
            $this->get('elcodi.object_manager.order')->flush();

            return $this->getJson([
                'OrderId' => $order->getId(),
                'Total' => $this->getDecimalPriceFromPrice($this->get('paymentsuite.bridge')),
                'Currency' => $this->get('paymentsuite.bridge')->getCurrency(),
            ]);

        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function restoreCartFromOrderAction(Request $request)
    {
        try
        {
            $id = $request->get('id');
            if ($id == '') {
                return $this->getFailedMessage('L\' id è necessario');
            }

            $this->get('elcodi.cart_restorer')->restoreCartFromOrderId($id);
            return $this->getSuccessMessage();
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    // UpdateCountryLanguage?countryId=3&language=1
    public function updateCountryLanguageAction(Request $request)
    {
        $countryId = $request->get('country_id');
        $lang = $request->get('lang');

        try
        {
            $this->setCountryId($countryId);
            $this->get('session')->set('lang', $lang);

            return $this->getSuccessMessage();
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function getCartAction(Request $request)
    {
        $response = array();

        try
        {
            $cart = $this
                ->get('elcodi.wrapper.cart')
                ->get();

            $this->updateCart();

            $store = $this->get('elcodi.wrapper.store')->get();
            $subTotal = Money::create(0, $store->getDefaultCurrency());

            foreach ($cart->getCartLines() as $cartLine) {
                $subTotal = $subTotal->add($cartLine->getAmount());
                $response['cartlines'][] = $this->getCartLine($cartLine);
            }
            $response['country_id'] = $this->get('session')->get('countryId');
            $response['currency'] = $this->get('session')->get('currency');
            $response['message'] = "success";
            $response['status'] = "OK";

            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $response['delivery_address'] = $this->getAddressData($cart->getDeliveryAddress(), $customer);

            $cartCoupons = $this
                ->get('elcodi.manager.cart_coupon')
                ->getCartCoupons($cart);

            if (count($cartCoupons) > 0) {
                $response['coupon'] = $cartCoupons[0]->getCoupon()->getCode();
            }

            // va fatto sul metodo effettivamente usato (vedi ordine padre)
            $response['shipping_cost'] = $this->getDecimalPriceFromPrice($cart->getShippingAmount());
            $response['sub_total'] = $this->getDecimalPriceFromPrice($subTotal);
            $response['coupon_total'] = $this->getDecimalPriceFromPrice($cart->getCouponAmount());
            $response['total'] = $this->getDecimalPriceFromPrice($cart->getAmount());

            return $this->getJson(
                $response
            );
        } catch (Exception $exception) {
            $array = array(
                'details' => [],
                'message' => "failed",
                'status' => "KO",
            );
            return $this->getJson(
                $array
            );
        }
    }

    public function removeCartLineAction(Request $request)
    {
        $cartLineId = $request->get('cart_line_id');
        try
        {
            $cart = $this
                ->get('elcodi.wrapper.cart')
                ->get();
            $cartLine = $this->get('elcodi.repository.cart_line')->find($cartLineId);
            $this
                ->get('elcodi.manager.cart')
                ->removeLine(
                    $cart,
                    $cartLine
                );
            return $this->getSuccessMessage();
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    protected function updateCart()
    {
        $cart = $this
            ->get('elcodi.wrapper.cart')
            ->get();
        $customer = $this
            ->get('elcodi.wrapper.customer')
            ->get();

        $this->setDeliveryAddressToCart($customer);
        $this
            ->get('elcodi.event_dispatcher.cart')
            ->dispatchCartLoadEvents($cart);
    }

    protected function getCartLine($cartLine)
    {
        return array(
            'cart_line_id' => $cartLine->getId(),
            'price' => $cartLine->getAmount()->getAmount() / 100,
            'quantity' => $cartLine->getQuantity(),
        );
    }

    public function addCouponToCartAction(Request $request)
    {
        try
        {
            $code = $request->get('code');
            if ($code == '') {
                return $this->getFailedMessage('Code needed');
            }

            $cart = $this->container->get('elcodi.wrapper.cart')->get();
            $result = $this
                ->container
                ->get('elcodi.manager.cart_coupon')
                ->addCouponByCode($cart, $code);

            return $this->getJson([
                'Message' => 'success',
                'Status' => 'OK',
            ]);
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function removeCouponFromCartAction(Request $request)
    {
        try
        {
            $code = $request->get('code');
            if ($code == '') {
                return $this->getFailedMessage('Code needed');
            }

            $cart = $this->container->get('elcodi.wrapper.cart')->get();

            $result = $this
                ->container
                ->get('elcodi.manager.cart_coupon')
                ->removeCouponByCode($cart, $code);

        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function updateCartLineQuantityAction(Request $request)
    {
        $cartLineId = $request->get('cart_line_id');
        $quantity = $request->get('quantity');

        try
        {
            $cart = $this
                ->get('elcodi.wrapper.cart')
                ->get();

            foreach ($cart->getCartLines() as $cartLine) {
                if ($cartLine->getId() == $cartLineId) {
                    $cartLine->setQuantity(intval($quantity));
                    $this
                        ->get('elcodi.object_manager.cart_line')
                        ->persist($cartLine);
                    $this
                        ->get('elcodi.object_manager.cart_line')
                        ->flush($cartLine);
                }
            }

            return $this->getSuccessMessage();
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function getCountriesAction(Request $request)
    {
        try
        {
            $countries = $this->get('elcodi.repository.country')->findByEnabled(true);

            $entityTranslator = $this->get('elcodi.entity_translator');
            $languages = $this->getLanguages();

            $results = array();
            foreach ($countries as $country) {
                $names = array();

                foreach ($languages as $language) {
                    $entityTranslator->translate($country, $language);
                    $names[] = [
                        'language_id' => $language,
                        'short' => $language,
                        'value' => $country->getName(),
                    ];
                }

                $results[] = [
                    'enabled' => true,
                    'id' => $country->getId(),
                    'name' => $names,
                ];
            }

            return $this->getJson(
                $results
            );
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function setDeliveryAddressAction(Request $request)
    {
        try
        {
            $addressId = $request->get('addressId');
            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $addresses = $customer
                ->getAddresses();

            foreach ($addresses as $address) {
                if ($address->getId() == $addressId) {
                    $customer->setDeliveryAddress($address);
                    $this->get('elcodi.object_manager.customer')->persist($customer);
                    $this->get('elcodi.object_manager.customer')->flush();

                    $country = $address->getCountry();
                    if ($country !== null) {
                        $this->setCountryId($country->getId());
                        $this->updateCart();
                        return $this->getSuccessMessage();
                    }
                }
            }

        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function getUserAddressesAction(Request $request)
    {
        try
        {
            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);

            $addresses = $customer
                ->getAddresses();

            $addressesFormatted = [];
            foreach ($addresses as $address) {
                $addressesFormatted[] = $this->getAddressData($address, $customer);
            }

            return $this->getJson(
                $addressesFormatted
            );
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    protected function getAddressData($address, $customer)
    {
        if ($address == null) {
            return [];
        }

        $countryId = 0;
        if ($address->getCountry()) {
            $countryId = $address->getCountry()->getId();
        }

        $defaultAddress = false;
        if ($customer->getDeliveryAddress() != null && $customer->getDeliveryAddress()->getId() == $address->getId()) {
            $defaultAddress = true;
        }

        return [
            'city' => $address->getCity(),
            'country' => $countryId,
            'default' => $defaultAddress,
            'id' => $address->getId(),
            'recipient_name' => $address->getRecipientName(),
            'name' => $address->getName(),
            'phone' => $address->getPhone(),
            'address' => $address->getAddress(),
            'address_more' => $address->getAddressMore(),
            'recipient_surname' => $address->getRecipientSurname(),
            'comments' => $address->getComments(),
            'postalcode' => $address->getPostalcode(),
        ];
    }

    public function createAddressAction(Request $request)
    {
        try
        {
            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);

            $name = $request->get('name');
            $surname = $request->get('surname');
            $company = $request->get('company');
            $street1 = $request->get('street1');
            $street2 = $request->get('street2');
            $streetNumber = $request->get('street_number');
            $zip = $request->get('zip');
            $city = $request->get('city');
            $countryId = $request->get('country_id');
            $vat = $request->get('vat');
            $phone = $request->get('phone');
            $nickname = $request->get('nickname');
            $flagDefault = $request->get('flag_default');
            $taxCode = $request->get('tax_code');
            $comments = $request->get('comments');

            $country = $this->get('elcodi.repository.country')->find($countryId);

            $addressFactory = $this->get('elcodi.factory.address');
            $address = $addressFactory->create();
            $address->setName($nickname);
            $address->setRecipientName($name);
            $address->setRecipientSurname($surname);
            $address->setAddress(trim($street1 . " " . $street2));
            $address->setAddressMore($streetNumber);
            $address->setPhone($phone);
            $address->setCountry($country);
            // $address->setMobile($mobile);
            $address->setComments($comments);
            $address->setCity($city);
            $address->setPostalCode($zip);

            // se è il primo indirizzo cambio country in uso
            $addresses = $customer->getAddresses();
            if (count($addresses) == 0) {
                $this->setCountryId($countryId);
            }

            $this->get('elcodi.object_manager.address')->persist($address);
            $this->get('elcodi.object_manager.address')->flush();

            $customer->addAddress($address);
            $this->get('elcodi.object_manager.customer')->persist($customer);
            $this->get('elcodi.object_manager.customer')->flush();

            $message = $this->getSuccessMessageArray();
            $message['address_id'] = $address->getId();
            return $this->getJson($message);
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    public function deleteUserAddressAction(Request $request)
    {
        try
        {
            $addressId = $request->get('address_id');

            $customer = $this
                ->get('elcodi.wrapper.customer')
                ->get();

            $this->throwExceptionIfInvalidCustomer($customer);

            $address = $this->get('elcodi.repository.address')->find($addressId);
            if ($address === null) {
                throw new Exception("No address found", 1);
            }

            $customer->removeAddress($address);

            $this->get('elcodi.object_manager.customer')->persist($customer);
            $this->get('elcodi.object_manager.customer')->flush();

            $this->get('elcodi.object_manager.address')->remove($address);
            $this->get('elcodi.object_manager.address')->flush();

            $message = $this->getSuccessMessageArray();
            $message['address_id'] = $address->getId();
            return $this->getJson($message);
        } catch (Exception $exception) {
            return $this->getFailedMessage($exception->getMessage());
        }
    }

    protected function createPaypalOrder()
    {
        $formView = $this
            ->get('paymentsuite.paypal_web_checkout.manager')
            ->generatePaypalForm();

        $this->get('paymentsuite.order.payment_setter')->setPaymentInOrder('elcodi_plugin.paypal_web_checkout.getter');
    }

    protected function throwExceptionIfNoProductsInCart()
    {
        $cart = $this
            ->get('elcodi.wrapper.cart')
            ->get();
        if (count($cart->getCartLines()) == 0) {
            throw new Exception("No products in cart");
        }
    }

    protected function setDeliveryAddressToCart($customer)
    {
        $cart = $this
            ->get('elcodi.wrapper.cart')
            ->get();

        $deliveryAddress = $this->getCustomerDeliveryAddress($customer);

        if ($deliveryAddress !== null) {
            $cart->setDeliveryAddress($deliveryAddress);
            $this->changeCountryUsingCustomer($customer);
            return;
        }

        return;
    }

    protected function authenticate($request, $user, $password)
    {
        // Here, "store_area" is the name of the firewall in your security.yml
        $token = new UsernamePasswordToken($user, $password, "store_area", $user->getRoles());
        $this->get("security.token_storage")->setToken($token);

        // Fire the login event
        // Logging the user in above the way we do it doesn't do this automatically
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }

    protected function throwExceptionIfInvalidCustomer($customer)
    {
        if ($customer === null || $customer->getId() === null) {
            throw new Exception("No customer found", 1);
        }
    }

    protected function getUserByEmail($email)
    {
        return $this
            ->get('elcodi.repository.customer')
            ->findOneBy([
                'email' => $email,
            ]);
    }

    protected function getLanguages()
    {
        return ['it', 'en', 'fr', 'nl'];
    }

    protected function getDefaultCurrency($tenant)
    {
        $store = $this->get('elcodi.wrapper.store')->get();
        return $store->getDefaultCurrency()->getIso();
    }

    protected function getDecimalPriceFromPrice($price)
    {
        $decimalPrice = $price->getAmount() / 100;
        return $decimalPrice;
    }

    protected function getFailedMessage($message = 'failed')
    {
        return $this->getJson(
            array(
                'message' => $message,
                'status' => "KO",
            )
        );
    }

    protected function getSuccessMessage()
    {
        return $this->getJson($this->getSuccessMessageArray());
    }

    protected function getSuccessMessageArray()
    {
        return array(
            'message' => "success",
            'status' => "OK",
        );
    }

    protected function getJson($array)
    {
        $response = new \Symfony\Component\HttpFoundation\JsonResponse($array);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    protected function changeCountryUsingCustomer($customer)
    {
        $deliveryAddress = $this->getCustomerDeliveryAddress($customer);
        if ($deliveryAddress !== null) {
            return $this->changeCountryUsingAddress($deliveryAddress);
        }
    }

    protected function getCustomerDeliveryAddress($customer)
    {
        if ($customer->getDeliveryAddress() !== null) {
            return $customer->getDeliveryAddress();
        }

        $addresses = $customer->getAddresses();
        if (count($addresses) > 0) {
            return $addresses[0];
        }
        return null;
    }

    protected function changeCountryUsingAddress($address)
    {
        $country = $address->getCountry();
        if ($country !== null) {
            $this->setCountryId($country->getId());
        }
    }

    protected function getDateString($date)
    {
        if ($date == null) {
            return '';
        }

        return $date->format('d/m/Y');
    }

    protected function getDateTimeString($date)
    {
        if ($date == null) {
            return '';
        }

        return $date->format('d/m/Y H:i');
    }

    protected function setCountryId($countryId)
    {
        $country = $this->get('elcodi.repository.country')->find($countryId);
        $this->get('session')->set('countryId', $countryId);
    }
}

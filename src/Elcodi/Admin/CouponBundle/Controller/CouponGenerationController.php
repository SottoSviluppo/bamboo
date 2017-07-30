<?php

namespace Elcodi\Admin\CouponBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Core\Entity\Interfaces\EnabledInterface;
use Elcodi\Component\Coupon\Entity\Interfaces\CouponInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller for Coupon
 *
 * @Route(
 *      path = "/coupon",
 * )
 */
class CouponGenerationController extends AbstractAdminController
{

    /**
     * @Route(
     *      path = "/coupon-generation",
     *      name = "admin_coupon_generation",
     *      methods = {"GET"}
     * )
     * @Template
     */
    public function couponGenerationAction()
    {
        $form = $this->createForm('elcodi_admin_coupon_generation_form_type_coupon');
        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route(
     *      path = "/coupon-generation-elaborate",
     *      name = "admin_coupon_generation_elaborate",
     *      methods = {"POST"}
     * )
     * @Template
     */
    public function couponGenerationElaborateAction(Request $request)
    {
        $formArray = $request->request->get('elcodi_admin_coupon_generation_form_type_coupon');

        $currency = $this->container->get('elcodi.repository.currency')->findOneByIso($formArray['price']['currency']);
        $amount = str_replace(',', '.', $formArray['price']['amount']);
        $amount = $amount * $currency->getDivideBy();

        $count = $formArray['count'];
        $chars = $formArray['chars'];

        $couponCampaign = $this->container->get('elcodi.repository.coupon_campaign')->find($formArray['coupon_campaign']);
        $this->container->get('elcodi.generator_manager.coupon')->setCouponCampaign($couponCampaign);
        $this->container->get('elcodi.generator_manager.coupon')->setAmount($amount);
        $this->container->get('elcodi.generator_manager.coupon')->setChars($chars);
        $this->container->get('elcodi.generator_manager.coupon')->setBaseName($couponCampaign->getCampaignName());
        $this->container->get('elcodi.generator_manager.coupon')->setFreeShipping($formArray['free_shipping']);
        $this->container->get('elcodi.generator_manager.coupon')->setStackable($formArray['stackable']);

        $start = null;
        if ($formArray['validFrom']['date'] != '') {
            $start = str_replace('-', '', $formArray['validFrom']['date']);
        }

        $end = null;
        if ($formArray['validTo']['date'] != '') {
            $end = str_replace('-', '', $formArray['validTo']['date']);
        }
        $this->container->get('elcodi.generator_manager.coupon')->setStart($start);
        $this->container->get('elcodi.generator_manager.coupon')->setEnd($end);
        $this->container->get('elcodi.generator_manager.coupon')->setColor($formArray['color']);

        $money = \Elcodi\Component\Currency\Entity\Money::create(
            $amount,
            $currency
        );

        $coupons = $this->container->get('elcodi.generator_manager.coupon')->generateCoupons($count, $money);
        $content = '';
        foreach ($coupons as $coupon) {
            $content .= $coupon->getCode() . "\r\n";
        }
        $this->container->get('elcodi.download_utility')->downloadStringFile($content, 'coupons_' . date('d_m_Y_Hi') . ' .txt');
        die();
        // return [
        //     'coupons' => $coupons,
        // ];
    }
}

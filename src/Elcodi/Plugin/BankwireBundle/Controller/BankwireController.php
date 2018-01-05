<?php

namespace Elcodi\Plugin\BankwireBundle\Controller;

use Elcodi\Component\Cart\Entity\Interfaces\OrderInterface;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * BankwireController
 *
 * @Security("has_role('ROLE_CUSTOMER')")
 * @Route(
 *      path = "/order",
 * )
 */
class BankwireController extends Controller
{
    use TemplateRenderTrait;

    /**
     * Order view
     *
     * @param integer $id     Order id
     * @param boolean $thanks Thanks
     *
     * @return Response Response
     *
     */
    public function viewAction($id, $thanks)
    {
        $order = $this->get('elcodi.repository.order')->findOneBy([
            'id' => $id,
            'customer' => $this->getUser(),
        ]);

        if (!($order instanceof OrderInterface)) {
            throw $this->createNotFoundException('Order not found');
        }

        $orderCoupons = $this
            ->get('elcodi.repository.order_coupon')
            ->findOrderCouponsByOrder($order);

        return $this->renderTemplate(
            'Pages:bankwire-order-view.html.twig',
            [
                'order' => $order,
                'orderCoupons' => $orderCoupons,
                'thanks' => $thanks,
            ]
        );
    }

}

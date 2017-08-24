<?php

namespace Elcodi\Admin\CoreBundle\Controller;

use Mmoreram\ControllerExtraBundle\Annotation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ComboController extends Controller
{
    /**
     *
     * @Route(
     *      path = "get-customer-for-combo/",
     *      name = "get_customer_for_combo"
     * )
     * @JsonResponse()
     */
    public function getCustomerForComboAction(Request $request)
    {
        $query = $request->get('query');
        $list = $this->getCustomerListFormCombo($query);

        return array(
            'data' => $list,
        );

    }

    public function getCustomerListFormCombo($query)
    {
        $em = $this->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $qb->from('Elcodi\Component\User\Entity\Customer', 'l');
        $qb->select('l.id, l.firstname, l.lastname, l.email');

        $searchKeywords = explode(' ', $query);
        $count = 0;
        foreach ($searchKeywords as $searchKeyword) {
            $keywordToUse = trim($searchKeyword);
            if (strlen($keywordToUse) == 0) {
                continue;
            }
            $qb->orWhere('l.firstname LIKE :search' . $count);
            $qb->orWhere('l.lastname LIKE :search' . $count);
            $qb->orWhere('l.email LIKE :search' . $count);
            $qb->setParameter('search' . $count, '%' . $keywordToUse . '%');
            ++$count;
        }

        $query = $qb->getQuery();
        // to debug
        // echo $query->getSql();
        // print_r($query->getParameters());die();

        $list = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        foreach ($list as $key => $value) {
            $toString = $value['firstname'] . ' ' . $value['lastname'] . ' - ' . $value['email'];
            $list[$key]['toString'] = trim($toString);
        }

        return $list;
    }
}

<?php

namespace Elcodi\Store\APIBundle\Services;

class CheckCustomerManager
{
    public $customerRepository;

    public function __construct($customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function existsEmail($email)
    {
        $user = $this->customerRepository->findOneByEmail($email);
        return $user !== null;
    }

    public function existsVat($vat)
    {
        $user = $this->customerRepository->findOneByVat($vat);
        return $user !== null;
    }
}

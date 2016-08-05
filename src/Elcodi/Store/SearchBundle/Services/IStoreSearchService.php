<?php

namespace Elcodi\Store\SearchBundle\Services;

interface IStoreSearchService
{
    function searchProducts($query);

    function searchOrders($query);

    function searchCustomers($query);
}
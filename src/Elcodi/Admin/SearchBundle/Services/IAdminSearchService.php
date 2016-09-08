<?php

namespace Elcodi\Admin\SearchBundle\Services;

interface IAdminSearchService
{
    function searchProducts($query, $page = 1, $limit = null);

    function searchOrders($query);

    function searchCustomers($query);

    function searchManufacturers($query);

    function getLimit();
}
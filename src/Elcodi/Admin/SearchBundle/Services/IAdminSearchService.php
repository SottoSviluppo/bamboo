<?php

namespace Elcodi\Admin\SearchBundle\Services;

interface IAdminSearchService
{
    function searchProducts($query, $page = 1, $limit = null);

    function searchOrders($query, $page = 1, $limit = null, array $dateRange = array());

    function searchCustomers($query, $page = 1, $limit = null);

    function searchManufacturers($query, $page = 1, $limit = null);

    function getLimit();
}
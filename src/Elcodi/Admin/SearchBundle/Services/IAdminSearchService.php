<?php

namespace Elcodi\Admin\SearchBundle\Services;

interface IAdminSearchService
{
    function searchProducts($query);

    function searchOrders($query);

    function searchCustomers($query);

    function searchManufacturers($query);
}
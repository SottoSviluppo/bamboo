<?php

namespace Elcodi\Store\SearchBundle\Services;

interface IStoreSearchService
{
    function searchProducts($query, $categories = array(), $priceRange = array());
}
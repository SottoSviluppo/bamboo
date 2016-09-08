<?php

namespace Elcodi\Store\SearchBundle\Services;

interface IStoreSearchService
{
    function searchProducts($query, $page = 1, $limit = null, $categories = array(), $priceRange = array());
}
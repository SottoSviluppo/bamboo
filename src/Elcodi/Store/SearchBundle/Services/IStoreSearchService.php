<?php

namespace Elcodi\Store\SearchBundle\Services;

interface IStoreSearchService
{
    function searchProducts($query, $page = 1, $limit = null, array $categories = array(), array $priceRange = array(), $categoryConnector = null);
}
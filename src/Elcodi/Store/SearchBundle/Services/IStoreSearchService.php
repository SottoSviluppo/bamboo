<?php

namespace Elcodi\Store\SearchBundle\Services;

interface IStoreSearchService
{
    function searchProducts($query, $page = 1, $limit = 20, $categories = array(), $priceRange = array());
}
<?php

namespace Elcodi\Store\ProductBundle\Services;

use Elcodi\Component\Core\Wrapper\Abstracts\AbstractCacheWrapper;
use Elcodi\Component\Language\Entity\Interfaces\LocaleInterface;
use Elcodi\Component\Product\Entity\Category;
use Elcodi\Component\Product\Services\CategoryTree;

class PackManager
{
    public $packRepository;
    public $em;

    public function __construct(
        $packRepository,
        $em
    ) {
        $this->packRepository = $packRepository;
        $this->em = $em;
    }

    public function findPacksWithPurchasable($purchasable)
    {
        $packs = $this->packRepository->getPacksWithPurchasableId($purchasable->getId());
        return $packs;

    }

}

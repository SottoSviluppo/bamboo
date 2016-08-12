<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Fixtures\DataFixtures\ORM\Country;

use Doctrine\Common\Persistence\ObjectManager;

use Elcodi\Bundle\CoreBundle\DataFixtures\ORM\Abstracts\AbstractFixture;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;

/**
 * Class CountryData
 */
class CountryData extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $spain = $this
            ->getFactory('country')
            ->create()
            ->setName('Spain')
            ->setEnabled(true);

        $manager->persist($spain);
        $this->addReference('spain', $spain);

        $italy = $this
            ->getFactory('country')
            ->create()
            ->setName('Italy')
            ->setEnabled(true);

        $manager->persist($italy);
        $this->addReference('italy', $italy);

        $manager->flush();
    }
}

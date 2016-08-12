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

namespace Elcodi\Fixtures\DataFixtures\ORM\Address;

use Doctrine\Common\Persistence\ObjectManager;

use Elcodi\Bundle\CoreBundle\DataFixtures\ORM\Abstracts\AbstractFixture;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;


use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// use Doctrine\Common\Persistence\ObjectManager;

// use Elcodi\Bundle\CoreBundle\DataFixtures\ORM\Abstracts\AbstractFixture;
// use Elcodi\Bundle\MediaBundle\DataFixtures\ORM\Traits\ImageManagerTrait;
// use Elcodi\Component\Core\Services\ObjectDirector;
// use Elcodi\Component\Store\Entity\Interfaces\StoreInterface;


/**
 * Class AddressData
 */
// class AddressData extends AbstractFixture
class AddressData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $spain = $this->getReference('spain');

        /**
         * @var AddressInterface $homeAddress
         */
        $homeAddress = $this
            ->getFactory('address')
            ->create()
            ->setName('My home')
            ->setRecipientName('Maggie')
            ->setRecipientSurname('Simpson')
            ->setCity('ES_CT_B_Barcelona')
            ->setCountry($spain)
            ->setPostalcode('08007')
            ->setAddress('Passeig de Gràcia')
            ->setAddressMore('1')
            ->setPhone('936524596')
            ->setMobile('625452365')
            ->setComments('Is an Apple Store')
            ->setEnabled(true);

        $manager->persist($homeAddress);
        $this->addReference('address-home', $homeAddress);

        /**
         * @var AddressInterface $workAddress
         */
        $workAddress = $this
            ->getFactory('address')
            ->create()
            ->setName('Work')
            ->setRecipientName('Homer')
            ->setRecipientSurname('Simpson')
            ->setCity('ES_CT_B_Barcelona')
            ->setCountry($spain)
            ->setPostalcode('08009')
            ->setAddress('C/ València')
            ->setAddressMore('333 Baixos')
            ->setPhone('935864758')
            ->setMobile('625452365')
            ->setComments('It\'s an office')
            ->setEnabled(true);

        $manager->persist($workAddress);
        $this->addReference('address-work', $workAddress);

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'Elcodi\Fixtures\DataFixtures\ORM\Country\CountryData',
        ];
    }
}

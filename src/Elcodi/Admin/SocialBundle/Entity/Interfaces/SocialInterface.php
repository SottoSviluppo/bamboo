<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi Networks S.L.
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

namespace Elcodi\Admin\SocialBundle\Entity\Interfaces;

use Elcodi\Component\Core\Entity\Interfaces\DateTimeInterface;
use Elcodi\Component\Core\Entity\Interfaces\EnabledInterface;
use Elcodi\Component\Core\Entity\Interfaces\IdentifiableInterface;

/**
 * Interface SocialInterface.
 */
interface SocialInterface
    extends
    IdentifiableInterface,
    DateTimeInterface,
    EnabledInterface

{

    /**
     * Sets Name.
     *
     * @param string $name Name
     *
     * @return $this Self object
     */
    public function setName($name);

    /**
     * Get Name.
     *
     * @return string Name
     */
    public function getName();

    /**
     * Sets Url.
     *
     * @param string $url Url
     *
     * @return $this Self object
     */
    public function setUrl($url);

    /**
     * Get Url.
     *
     * @return string Url
     */
    public function getUrl();

    /**
     * Sets Position.
     *
     * @param string $position Position
     *
     * @return $this Self object
     */
    public function setPosition($position);

    /**
     * Get Position.
     *
     * @return string Position
     */
    public function getPosition();

    /**
     * Sets Class.
     *
     * @param string $class Class
     *
     * @return $this Self object
     */
    public function setClass($class);

    /**
     * Get Class.
     *
     * @return string Class
     */
    public function getClass();



}

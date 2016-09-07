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

namespace Elcodi\Admin\SocialBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Elcodi\Component\Core\Entity\Traits\DateTimeTrait;
use Elcodi\Component\Core\Entity\Traits\EnabledTrait;
use Elcodi\Component\Core\Entity\Traits\IdentifiableTrait;
use Elcodi\Admin\SocialBundle\Entity\Interfaces\SocialInterface;

/**
 * Class Social.
 */
class Social implements SocialInterface
{
    use IdentifiableTrait, 
    DateTimeTrait, 
    EnabledTrait
    ;

    /**
     * @var string
     *
     */
    protected $name;

    /**
     * @var string
     *
     */
    protected $url;

    /**
     * @var integer
     *
     */
    protected $position;

    /**
     * @var string
     *
     */
    protected $class;



	public function __construct()
    {
    }

    /**
     * Sets Name.
     *
     * @param string $name
     *
     * @return $this Self object
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name.
     *
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets Url.
     *
     * @param string $url
     *
     * @return $this Self object
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get Url.
     *
     * @return string Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets Position.
     *
     * @param integer $position
     *
     * @return $this Self object
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get Position.
     *
     * @return integer Position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets Class.
     *
     * @param string $class
     *
     * @return $this Self object
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get Class.
     *
     * @return string Class
     */
    public function getClass()
    {
        return $this->class;
    }






    public function __toString()
    {
        return "Gestione social";
    }

}

<?php

namespace Elcodi\Common\TranslationBundle\Entity;

use Elcodi\Common\TranslationBundle\Manager\TranslationInterface;
use Elcodi\Common\TranslationBundle\Model\Translation as TranslationModel;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(fields={"transUnit", "locale"})
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class Translation extends TranslationModel implements TranslationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Elcodi\Common\TranslationBundle\Entity\TransUnit
     */
    protected $transUnit;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transUnit
     *
     * @param Elcodi\Common\TranslationBundle\Entity\TransUnit $transUnit
     */
    public function setTransUnit(\Elcodi\Common\TranslationBundle\Model\TransUnit $transUnit)
    {
        $this->transUnit = $transUnit;
    }

    /**
     * Get transUnit
     *
     * @return Elcodi\Common\TranslationBundle\Entity\TransUnit
     */
    public function getTransUnit()
    {
        return $this->transUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist()
    {
        $now = new \DateTime("now");
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }
}

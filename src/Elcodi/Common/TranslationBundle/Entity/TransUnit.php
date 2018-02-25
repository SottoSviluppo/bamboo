<?php

namespace Elcodi\Common\TranslationBundle\Entity;

use Elcodi\Common\TranslationBundle\Manager\TransUnitInterface;
use Elcodi\Common\TranslationBundle\Model\TransUnit as TransUnitModel;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(fields={"key", "domain"})
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class TransUnit extends TransUnitModel implements TransUnitInterface
{
    /**
     * Add translations
     *
     * @param Elcodi\Common\TranslationBundle\Entity\Translation $translations
     */
    public function addTranslation(\Elcodi\Common\TranslationBundle\Model\Translation $translation)
    {
        $translation->setTransUnit($this);

        $this->translations[] = $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }
}

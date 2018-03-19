<?php

namespace Elcodi\Common\TranslationBundle\Document;

use Elcodi\Common\TranslationBundle\Manager\TranslationInterface;
use Elcodi\Common\TranslationBundle\Model\Translation as TranslationModel;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class Translation extends TranslationModel implements TranslationInterface
{
    /**
     * Convert all MongoTimestamp object to time.
     */
    public function convertMongoTimestamp()
    {
        $this->createdAt = ($this->createdAt instanceof \MongoTimestamp) ? $this->createdAt->sec : $this->createdAt;
        $this->updatedAt = ($this->updatedAt instanceof \MongoTimestamp) ? $this->updatedAt->sec : $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist()
    {
        $now = new \DateTime("now");

        $this->createdAt = $now->format('U');
        $this->updatedAt = $now->format('U');
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate()
    {
        $now = new \DateTime("now");

        $this->updatedAt = $now->format('U');
    }
}

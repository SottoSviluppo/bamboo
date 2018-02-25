<?php

namespace Elcodi\Common\TranslationBundle\Document;

use Elcodi\Common\TranslationBundle\Manager\FileInterface;
use Elcodi\Common\TranslationBundle\Model\File as FileModel;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class File extends FileModel implements FileInterface
{
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

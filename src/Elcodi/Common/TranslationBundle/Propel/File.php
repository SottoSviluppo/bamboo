<?php

namespace Elcodi\Common\TranslationBundle\Propel;

use Elcodi\Common\TranslationBundle\Manager\FileInterface;
use Elcodi\Common\TranslationBundle\Propel\Base\File as BaseFile;

class File extends BaseFile implements FileInterface
{
    /**
     * Set file name
     *
     * @param string $name
     */
    public function setName($name)
    {
        list($domain, $locale, $extention) = explode('.', $name);

        $this
            ->setDomain($domain)
            ->setLocale($locale)
            ->setExtention($extention)
        ;

        return $this;
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getName()
    {
        return sprintf('%s.%s.%s', $this->getDomain(), $this->getLocale(), $this->getExtention());
    }
}

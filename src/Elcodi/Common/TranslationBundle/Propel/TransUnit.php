<?php

namespace Elcodi\Common\TranslationBundle\Propel;

use Elcodi\Common\TranslationBundle\Manager\TranslationInterface;
use Elcodi\Common\TranslationBundle\Manager\TransUnitInterface;
use Elcodi\Common\TranslationBundle\Model\Translation;
use Elcodi\Common\TranslationBundle\Propel\Base\TransUnit as BaseTransUnit;

class TransUnit extends BaseTransUnit implements TransUnitInterface
{
    protected $translations = array();

    /**
     * Return translations with  not blank content.
     *
     * @return array
     */
    public function filterNotBlankTranslations()
    {
        return array_filter($this->getTranslations()->getArrayCopy(), function (TranslationInterface $translation) {
            $content = $translation->getContent();

            return !empty($content);
        });
    }

    /** (non-PHPdoc)
     * @see \Elcodi\Common\TranslationBundle\Manager\TransUnitInterface::hasTranslation()
     */
    public function hasTranslation($locale)
    {
        return null !== $this->getTranslation($locale);
    }

    /**
     * Return the content of translation for the given locale.
     *
     * @param string $locale
     * @return Translation
     */
    public function getTranslation($locale)
    {
        foreach ($this->getTranslations() as $translation) {
            if ($translation->getLocale() == $locale) {
                return $translation;
            }
        }

        return null;
    }
}

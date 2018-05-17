<?php

namespace Elcodi\Common\TranslationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Elcodi\Common\TranslationBundle\Manager\TranslationInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class represent a trans unit which contain translations for a given domain and key.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
abstract class TransUnit
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $key;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $domain;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $translations;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->domain = 'messages';
        $this->translations = new ArrayCollection();
    }

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
     * Set key name
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key name
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set domain
     *
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Add translations
     *
     * @param \Elcodi\Common\TranslationBundle\Model\Translation $translations
     */
    public function addTranslation(\Elcodi\Common\TranslationBundle\Model\Translation $translation)
    {
        $this->translations[] = $translation;
    }

    /**
     * Remove translations
     *
     * @param \Elcodi\Common\TranslationBundle\Model\Translation $translations
     */
    public function removeTranslation(\Elcodi\Common\TranslationBundle\Model\Translation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Return true if this object has a translation for the given locale.
     *
     * @param string $locale
     * @return boolean
     */
    public function hasTranslation($locale)
    {
        return null !== $this->getTranslation($locale);
    }

    /**
     * Return the content of translation for the given locale.
     *
     * @param string $locale
     * @return \Elcodi\Common\TranslationBundle\Model\Translation
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

    /**
     * Set translations collection
     *
     * @param Collection $collection
     */
    public function setTranslations(Collection $collection)
    {
        $this->translations = new ArrayCollection();

        foreach ($collection as $translation) {
            $this->addTranslation($translation);
        }
    }

    /**
     * Return transaltions with  not blank content.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function filterNotBlankTranslations()
    {
        return $this->getTranslations()->filter(function (TranslationInterface $translation) {
            $content = $translation->getContent();
            return !empty($content);
        });
    }

    /**
     * Get createdAt
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
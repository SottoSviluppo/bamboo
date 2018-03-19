<?php

namespace Elcodi\Common\TranslationBundle\EventDispatcher;

use Elcodi\Common\TranslationBundle\EventDispatcher\Event\GetDatabaseResourcesEvent;
use Elcodi\Common\TranslationBundle\Storage\StorageInterface;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class GetDatabaseResourcesListener
{
    /**
     * @var \Elcodi\Common\TranslationBundle\Storage\StorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $storageType;

    /**
     * @param StorageInterface $storage
     * @param $storageType
     */
    public function __construct(StorageInterface $storage, $storageType)
    {
        $this->storage = $storage;
        $this->storageType = $storageType;
    }

    /**
     * Query the database to get translation resources and set it on the event.
     *
     * @param GetDatabaseResourcesEvent $event
     */
    public function onGetDatabaseResources(GetDatabaseResourcesEvent $event)
    {
        // prevent errors on command such as cache:clear if doctrine schema has not been updated yet
        if (StorageInterface::STORAGE_ORM == $this->storageType && !$this->storage->translationsTablesExist()) {
            $resources = array();
        } else {
            $resources = $this->storage->getTransUnitDomainsByLocale();
        }

        $event->setResources($resources);
    }
}

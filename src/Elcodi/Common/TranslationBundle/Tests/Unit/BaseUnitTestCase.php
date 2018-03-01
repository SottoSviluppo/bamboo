<?php

namespace Elcodi\Common\TranslationBundle\Tests\Unit;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\DataFixtures\Executor\MongoDBExecutor;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\Setup;
use Elcodi\Common\TranslationBundle\Storage\DoctrineMongoDBStorage;
use Elcodi\Common\TranslationBundle\Storage\DoctrineORMStorage;
use Elcodi\Common\TranslationBundle\Storage\PropelStorage;
use Elcodi\Common\TranslationBundle\Tests\Fixtures\TransUnitData;
use Elcodi\Common\TranslationBundle\Tests\Fixtures\TransUnitDataPropel;
use Propel\Generator\Util\QuickBuilder;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Connection\PdoConnection;
use Propel\Runtime\Propel;

/**
 * Base unit test class providing functions to create a mock entity manger, load schema and fixtures.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
abstract class BaseUnitTestCase extends \PHPUnit_Framework_TestCase
{
    const ENTITY_TRANS_UNIT_CLASS = 'Elcodi\Common\TranslationBundle\Entity\TransUnit';
    const ENTITY_TRANSLATION_CLASS = 'Elcodi\Common\TranslationBundle\Entity\Translation';
    const ENTITY_FILE_CLASS = 'Elcodi\Common\TranslationBundle\Entity\File';

    const DOCUMENT_TRANS_UNIT_CLASS = 'Elcodi\Common\TranslationBundle\Document\TransUnit';
    const DOCUMENT_TRANSLATION_CLASS = 'Elcodi\Common\TranslationBundle\Document\Translation';
    const DOCUMENT_FILE_CLASS = 'Elcodi\Common\TranslationBundle\Document\File';

    const PROPEL_TRANS_UNIT_CLASS = 'Elcodi\Common\TranslationBundle\Propel\TransUnit';
    const PROPEL_TRANSLATION_CLASS = 'Elcodi\Common\TranslationBundle\Propel\Translation';
    const PROPEL_FILE_CLASS = 'Elcodi\Common\TranslationBundle\Propel\File';

    /**
     * Create a storage class form doctrine ORM.
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @return \Elcodi\Common\TranslationBundle\Storage\DoctrineORMStorage
     */
    protected function getORMStorage(\Doctrine\ORM\EntityManager $em)
    {
        $registryMock = $this->getDoctrineRegistryMock($em);

        $storage = new DoctrineORMStorage($registryMock, 'default', array(
            'trans_unit' => self::ENTITY_TRANS_UNIT_CLASS,
            'translation' => self::ENTITY_TRANSLATION_CLASS,
            'file' => self::ENTITY_FILE_CLASS,
        ));

        return $storage;
    }

    /**
     * Create a storage class form doctrine Mongo DB.
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     * @return \Elcodi\Common\TranslationBundle\Storage\DoctrineORMStorage
     */
    protected function getMongoDBStorage(\Doctrine\ODM\MongoDB\DocumentManager $dm)
    {
        $registryMock = $this->getDoctrineRegistryMock($dm);

        $storage = new DoctrineMongoDBStorage($registryMock, 'default', array(
            'trans_unit' => self::DOCUMENT_TRANS_UNIT_CLASS,
            'translation' => self::DOCUMENT_TRANSLATION_CLASS,
            'file' => self::DOCUMENT_FILE_CLASS,
        ));

        return $storage;
    }

    /**
     * Create a storage class for Propel.
     *
     * @return \Elcodi\Common\TranslationBundle\Storage\PropelStorage
     */
    protected function getPropelStorage()
    {
        $storage = new PropelStorage(null, array(
            'trans_unit' => self::PROPEL_TRANS_UNIT_CLASS,
            'translation' => self::PROPEL_TRANSLATION_CLASS,
            'file' => self::PROPEL_FILE_CLASS,
        ));

        return $storage;
    }

    /**
     * Create the database schema.
     *
     * @param ObjectManager $om
     */
    protected function createSchema(ObjectManager $om)
    {
        if ($om instanceof \Doctrine\ORM\EntityManager) {
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($om);
            $schemaTool->createSchema($om->getMetadataFactory()->getAllMetadata());
        } elseif ($om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
            $sm = new \Doctrine\ODM\MongoDB\SchemaManager($om, $om->getMetadataFactory());
            $sm->dropDatabases();
            $sm->createCollections();
        }
    }

    /**
     * Load test fixtures.
     *
     * @param ObjectManager $om
     */
    protected function loadFixtures(ObjectManager $om)
    {
        if ($om instanceof \Doctrine\ORM\EntityManager) {
            $purger = new ORMPurger();
            $executor = new ORMExecutor($om, $purger);
        } elseif ($om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
            $purger = new MongoDBPurger();
            $executor = new MongoDBExecutor($om, $purger);
        }

        $fixtures = new TransUnitData();
        $executor->execute(array($fixtures), false);
    }

    /**
     * Load test fixtures for Propel.
     */
    protected function loadPropelFixtures(ConnectionWrapper $con)
    {
        $fixtures = new TransUnitDataPropel();
        $fixtures->load($con);
    }

    /**
     * @param $om
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDoctrineRegistryMock($om)
    {
        $registryMock = $this->getMockBuilder('Symfony\Bridge\Doctrine\ManagerRegistry')
            ->setConstructorArgs(array(
                'registry',
                array(),
                array(),
                'default',
                'default',
                'proxy',
            ))
            ->getMock();

        $registryMock
            ->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($om))
        ;

        return $registryMock;
    }

    /**
     * EntityManager mock object together with annotation mapping driver and
     * pdo_sqlite database in memory
     *
     * @return EntityManager
     */
    protected function getMockSqliteEntityManager($mockCustomHydrator = false)
    {
        $cache = new \Doctrine\Common\Cache\ArrayCache();

        // xml driver
        $xmlDriver = new \Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver(array(
            __DIR__ . '/../../Resources/config/model' => 'Elcodi\Common\TranslationBundle\Model',
            __DIR__ . '/../../Resources/config/doctrine' => 'Elcodi\Common\TranslationBundle\Entity',
        ));

        $config = Setup::createAnnotationMetadataConfiguration(array(
            __DIR__ . '/../../Model',
            __DIR__ . '/../../Entity',
        ), false, null, null, false);

        $config->setMetadataDriverImpl($xmlDriver);
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setClassMetadataFactoryName('Doctrine\ORM\Mapping\ClassMetadataFactory');
        $config->setDefaultRepositoryClassName('Doctrine\ORM\EntityRepository');

        if ($mockCustomHydrator) {
            $config->setCustomHydrationModes(array(
                'SingleColumnArrayHydrator' => 'Elcodi\Common\TranslationBundle\Util\Doctrine\SingleColumnArrayHydrator',
            ));
        }

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $em = \Doctrine\ORM\EntityManager::create($conn, $config);

        return $em;
    }

    /**
     * Create a DocumentManager instance for tests.
     *
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getMockMongoDbDocumentManager()
    {
        $prefixes = array(
            __DIR__ . '/../../Resources/config/model' => 'Elcodi\Common\TranslationBundle\Model',
            __DIR__ . '/../../Resources/config/doctrine' => 'Elcodi\Common\TranslationBundle\Document',
        );
        $xmlDriver = new \Doctrine\Bundle\MongoDBBundle\Mapping\Driver\XmlDriver($prefixes);

        $cache = new \Doctrine\Common\Cache\ArrayCache();

        $config = new \Doctrine\ODM\MongoDB\Configuration();
        $config->setMetadataCacheImpl($cache);
        $config->setMetadataDriverImpl($xmlDriver);
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setClassMetadataFactoryName('Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory');
        $config->setDefaultDB('lexik_translation_bundle_test');
        $config->setHydratorDir(sys_get_temp_dir());
        $config->setHydratorNamespace('Doctrine\ODM\MongoDB\Hydrator');
        $config->setAutoGenerateHydratorClasses(true);
        $config->setDefaultCommitOptions(array());

        $options = array();
        $conn = new \Doctrine\MongoDB\Connection(null, $options, $config);

        $dm = \Doctrine\ODM\MongoDB\DocumentManager::create($conn, $config);

        return $dm;
    }

    /**
     * @return ConnectionWrapper
     */
    protected function getMockPropelConnection()
    {
        if (!class_exists('Lexik\\Bundle\\TranslationBundle\\Propel\\Base\\File')) {
            // classes are built in-memory.
            $builder = new QuickBuilder();
            $builder->setSchema(file_get_contents(__DIR__ . '/../../Resources/config/propel/schema.xml'));
            $con = $builder->build(null, null, null, null, array('tablemap', 'object', 'query'));
        } else {
            // in memory-classes already exist, create connection and SQL manually
            $dsn = 'sqlite::memory:';
            $pdoConnection = new PdoConnection($dsn);
            $con = new ConnectionWrapper($pdoConnection);
            Propel::getServiceContainer()->setConnection('default', $con);

            $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
            $builder = new QuickBuilder();
            $builder->setSchema(file_get_contents(__DIR__ . '/../../Resources/config/propel/schema.xml'));
            $builder->buildSQL($con);
        }

        return $con;
    }
}

<?php

namespace Elcodi\Common\TranslationBundle\Propel;

use Elcodi\Common\TranslationBundle\Propel\Map\TranslationTableMap;
use Propel\Runtime\Connection\ConnectionWrapper;

/**
 * Repository for Translation entity (Propel).
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class TranslationRepository
{
    /**
     * @var ConnectionWrapper
     */
    protected $connection;

    /**
     * @param ConnectionWrapper $connection
     */
    public function __construct(ConnectionWrapper $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return ConnectionWrapper
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return \DateTime|null
     */
    public function getLatestTranslationUpdatedAt()
    {
        $result = TranslationQuery::create()
            ->withColumn(sprintf('MAX(%s)', TranslationTableMap::COL_UPDATED_AT), 'max_updated_at')
            ->select(array('max_updated_at'))
            ->findOne($this->getConnection())
        ;

        return !empty($result) ? new \DateTime($result) : null;
    }
}

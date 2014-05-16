<?php

namespace Api\Service\Connection;

use Api\Service\AccessorTrait;
use Doctrine\ORM\EntityManager;
use Api\Entities\Connection;

/**
 * Class Save
 *
 * @package Api\Service\Connection
 *
 * @method Save          setEntityManager(EntityManager $entityManager)
 * @method EntityManager getEntityManager()
 */
class Save
{
    use AccessorTrait;

    /**
     * @param Connection $connection
     *
     * @throws \Exception
     */
    public function save(Connection $connection)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        try {
            $entityManager->persist($connection);
            $entityManager->flush($connection);

            $this->getEntityManager()->commit();
        } catch (\Exception $exc) {
            $this->getEntityManager()->rollback();

            throw $exc;
        }

        return $connection;
    }

}
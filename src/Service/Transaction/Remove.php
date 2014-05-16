<?php

namespace Api\Service\Transaction;

use Doctrine\ORM\EntityManager;
use Api\Entities\Transaction;
use Api\Service\AccessorTrait;

/**
 * Class Create
 *
 * @package Api\Service\Transaction
 *
 * @method Remove        setEntityManager(EntityManager $entityManager)
 * @method EntityManager getEntityManager()
 */
class Remove
{
    use AccessorTrait;

    /**
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function remove(
        Transaction $transaction
    ) {
        $entityManager = $this->getEntityManager();

        try {
            $entityManager->beginTransaction();

            $entityManager->remove($transaction);

            $entityManager->flush();

            $entityManager->commit();

        } catch (\Exception $exc) {
            $entityManager->rollback();
            return false;
        }

        return true;
    }

}

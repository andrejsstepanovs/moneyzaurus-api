<?php

namespace Api\Service\User;

use Doctrine\ORM\EntityManager;
use Api\Entities\User;
use Api\Service\AccessorTrait;

/**
 * Class Save
 *
 * @package Api\Service\User
 *
 * @method Save          setEntityManager(EntityManager $entityManager)
 * @method EntityManager getEntityManager()
 */
class Save
{
    use AccessorTrait;

    /**
     * @param User $user
     *
     * @return User
     * @throws \Exception
     */
    public function saveUser(User $user)
    {
        $this->getEntityManager()->beginTransaction();

        try {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush($user);
            $entityManager->commit();

        } catch (\Exception $exc) {
            $this->getEntityManager()->rollback();

            throw $exc;
        }

        return $user;
    }
}

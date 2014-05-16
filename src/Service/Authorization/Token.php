<?php

namespace Api\Service\Authorization;

use Api\Entities\User;
use Api\Entities\Connection;
use Api\Entities\AccessToken;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Api\Service\AccessorTrait;

/**
 * Class Token
 *
 * @package Api\Service\Authorization
 *
 * @method Token                        setEntityManager(EntityManager $entityManager)
 * @method Token                        setAccessToken(EntityRepository $accessToken)
 * @method Token                        setUser(User $user)
 * @method EntityManager                getEntityManager()
 * @method EntityRepository|AccessToken getAccessToken()
 * @method User                         getUser()
 */
class Token
{
    use AccessorTrait;

    /** Minimal token size */
    const MIN_TOKEN_SIZE = 32;

    /**
     * @param string $token
     *
     * @return User|null
     */
    public function findUser($token)
    {
        $criteria = array('token' => $token);

        /** @var \Doctrine\ORM\EntityRepository $user */
        $accessTokenRepository = $this->getEntityManager()->getRepository('Api\Entities\AccessToken');
        /** @var AccessToken $accessToken */
        $accessToken = $accessTokenRepository->findOneBy($criteria);

        /** @var User $user */
        $user = $accessToken ? $accessToken->getUser() : null;

        return $this->setUser($user)->getUser();
    }

    /**
     * @param User   $user
     * @param string $token
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function remove(User $user, $token)
    {
        $criteria = array(
            'token' => $token,
            'user'  => $user
        );

        /** @var \Doctrine\ORM\EntityRepository $accessTokenRepository */
        $accessTokenRepository = $this->getEntityManager()->getRepository('Api\Entities\AccessToken');

        $accessToken = $accessTokenRepository->findOneBy($criteria);
        if (!$accessToken) {
            throw new \RuntimeException('Access token not found');
        }

        return $this->removeAccessToken($accessToken);
    }

    /**
     * @param AccessToken $token
     *
     * @return bool
     */
    private function removeAccessToken(AccessToken $token)
    {
        $entityManager = $this->getEntityManager();

        try {
            $entityManager->beginTransaction();

            $entityManager->remove($token);

            $entityManager->flush();

            $entityManager->commit();

        } catch (\Exception $exc) {
            $entityManager->rollback();
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getConnectedUsers()
    {
        $parentsIds = array();
        $user = $this->getUser();
        if ($user) {
            /** @var \Doctrine\ORM\EntityRepository $connectionRepository */
            $connectionRepository = $this->getEntityManager()->getRepository('Api\Entities\Connection');
            $criteria = array('user' => $user->getId());
            $connections = $connectionRepository->findBy($criteria);

            /** @var Connection $connection */
            foreach ($connections as $connection) {
                $parentsIds[] = $connection->getParent()->getId();
            }
        }

        return $parentsIds;
    }

    /**
     * @param User $user
     *
     * @return AccessToken
     */
    public function get(User $user)
    {
        $token = $this->generateToken(strval($user->getId()));

        $this->getAccessToken()
            ->setToken($token)
            ->setUser($user)
            ->setCreated(new \DateTime());

        $this->getEntityManager()->persist($this->getAccessToken());
        $this->getEntityManager()->flush();

        return $this->getAccessToken();
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    private function generateToken($prefix)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789_-';

        return $prefix . substr(str_shuffle($characters), 0, self::MIN_TOKEN_SIZE);
    }
}
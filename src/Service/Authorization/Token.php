<?php

namespace Api\Service\Authorization;

use Api\Entities\User;
use Api\Entities\Connection;
use Api\Entities\AccessToken;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Api\Service\AccessorTrait;
use Api\Service\Time;
use Api\Service\Exception\TokenExpiredException;

/**
 * Class Token
 *
 * @package Api\Service\Authorization
 *
 * @method Token         setEntityManager(EntityManager $entityManager)
 * @method Token         setAccessToken(AccessToken $accessToken)
 * @method Token         setUser(User $user)
 * @method Token         setTime(Time $time)
 * @method Token         setTokenInterval($interval)
 * @method EntityManager getEntityManager()
 * @method AccessToken   getAccessToken()
 * @method User          getUser()
 * @method Time          getTime()
 * @method string        getTokenInterval()
 */
class Token
{
    use AccessorTrait;

    /** Minimal token size */
    const MIN_TOKEN_SIZE = 32;

    /**
     * @param string $token
     *
     * @return null|AccessToken
     */
    public function findAccessToken($token)
    {
        $criteria = array('token' => $token);

        /** @var \Doctrine\ORM\EntityRepository $user */
        $accessTokenRepository = $this->getEntityManager()->getRepository('Api\Entities\AccessToken');
        /** @var AccessToken $accessToken */
        return $accessTokenRepository->findOneBy($criteria);
    }

    /**
     * @param AccessToken $accessToken
     * @param User        $user
     *
     * @return $this
     * @throws TokenExpiredException
     */
    public function validateExpired(AccessToken $accessToken, User $user)
    {
        $userTimezone = new \DateTimeZone($user->getTimezone());

        $time    = $this->getTime()->setTimezone($userTimezone);
        $isValid = $time->compareDateTime($time->getDateTime(), $accessToken->getValidUntil());
        if (!$isValid) {
            throw new TokenExpiredException('Token has expired');
        }

        return $this;
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
    public function getConnectedUsers(User $user)
    {
        $parentsIds = [];
        if ($user) {
            /** @var \Doctrine\ORM\EntityRepository $connectionRepository */
            $connectionRepository = $this->getEntityManager()->getRepository('Api\Entities\Connection');
            $criteria = array(
                'user'  => $user->getId(),
                'state' => Connection::STATE_ACCEPTED
            );
            $connections = $connectionRepository->findBy($criteria);

            /** @var Connection $connection */
            foreach ($connections as $connection) {
                $parentsIds[] = $connection->getParent()->getId();
            }

            $criteria = array(
                'parent' => $user->getId(),
                'state'  => Connection::STATE_ACCEPTED
            );
            $connections = $connectionRepository->findBy($criteria);

            /** @var Connection $connection */
            foreach ($connections as $connection) {
                $parentsIds[] = $connection->getUser()->getId();
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

        $currentDateTime = new \DateTime();
        $this->getAccessToken()
            ->setToken($token)
            ->setUser($user)
            ->setCreated($currentDateTime)
            ->setUsedAt(null)
            ->setValidUntil($this->getInterval($currentDateTime));

        $this->getEntityManager()->persist($this->getAccessToken());
        $this->getEntityManager()->flush();

        return $this->getAccessToken();
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return \DateTime
     */
    public function getInterval(\DateTime $dateTime)
    {
        $interval = $this->getTokenInterval();
        $dateTime->add(new \DateInterval($interval));

        return $dateTime;
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

    /**
     * @param AccessToken $accessToken
     *
     * @return AccessToken
     * @throws \Exception
     */
    public function save(AccessToken $accessToken) {
        try {
            $entityManager = $this->getEntityManager();

            $entityManager->beginTransaction();
            $entityManager->persist($accessToken);
            $entityManager->flush($accessToken);

            $this->getEntityManager()->commit();
        } catch (\Exception $exc) {
            $this->getEntityManager()->rollback();

            throw $exc;
        }

        return $accessToken;
    }
}
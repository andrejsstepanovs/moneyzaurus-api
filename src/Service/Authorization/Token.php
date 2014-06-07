<?php

namespace Api\Service\Authorization;

use Api\Entities\User;
use Api\Entities\Connection;
use Api\Entities\AccessToken;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Api\Service\AccessorTrait;
use Api\Service\Time;

/**
 * Class Token
 *
 * @package Api\Service\Authorization
 *
 * @method Token         setEntityManager(EntityManager $entityManager)
 * @method Token         setAccessToken(AccessToken $accessToken)
 * @method Token         setUser(User $user)
 * @method Token         setTime(Time $time)
 * @method EntityManager getEntityManager()
 * @method AccessToken   getAccessToken()
 * @method User          getUser()
 * @method Time          getTime()
 */
class Token
{
    use AccessorTrait;

    /** Minimal token size */
    const MIN_TOKEN_SIZE = 32;

    /** Token valid time interval */
    const INTERVAL = 'P1Y';

    /**
     * @param string $token
     *
     * @return User
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
        $this->setUser($user);

        if ($user) {
            $this->validate($accessToken);
        }

        return $this->getUser();
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    private function validate(AccessToken $accessToken)
    {
        $userTimezone = new \DateTimeZone($this->getUser()->getTimezone());

        $time    = $this->getTime()->setTimezone($userTimezone);
        $isValid = $time->compareDateTime($time->getDateTime(), $accessToken->getValidUntil());
        if (!$isValid) {
            throw new \InvalidArgumentException('Token has expired');
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
    public function getConnectedUsers()
    {
        $parentsIds = array();
        $user = $this->getUser();
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
        $dateTime->add(new \DateInterval(self::INTERVAL));

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
}
<?php

namespace Api\Service\Connection;

use Api\Service\AccessorTrait;
use Egulias\EmailValidator\EmailValidator;
use Api\Entities\User;
use Api\Entities\Connection;
use Api\Service\User\Data as UserData;
use Api\Service\Locale;
use Doctrine\ORM\EntityRepository;
use \Doctrine\DBAL\LockMode;

/**
 * Class Data
 *
 * @package Api\Service\Connection
 *
 * @method Save             setEmailValidator(EmailValidator $emailValidator)
 * @method Save             setUserData(UserData $userData)
 * @method Save             setConnectionRepository(EntityRepository $group)
 * @method Save             setLocale(Locale $locale)
 * @method EntityRepository getConnectionRepository()
 * @method EmailValidator   getEmailValidator()
 * @method UserData         getUserData()
 * @method Locale           getLocale()
 */
class Data
{
    use AccessorTrait;

    /**
     * @param User $user
     *
     * @return Connection[]
     */
    public function findByUser(User $user)
    {
        $criteria = array(
            'user' => $user->getId()
        );

        return $this->getConnectionRepository()->findBy($criteria);
    }

    /**
     * @param User $user
     *
     * @return Connection[]
     */
    public function findByParent(User $user)
    {
        $criteria = array(
            'parent' => $user->getId()
        );

        return $this->getConnectionRepository()->findBy($criteria);
    }

    /**
     * @param string $email
     *
     * @return User
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getInvitedUser($email)
    {
        $isValidEmail = $this->getEmailValidator()->isValid($email);
        if (!$isValidEmail) {
            throw new \InvalidArgumentException('Email is not valid');
        }

        $user = $this->getUserData()->findUser($email);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $criteria = array('parent' => $user);
        $connection = $this->getConnectionRepository()->findOneBy($criteria);
        if ($connection !== null) {
            throw new \InvalidArgumentException('User is already invited');
        }

        $criteria = array('user' => $user);
        $connection = $this->getConnectionRepository()->findOneBy($criteria);
        if ($connection !== null) {
            throw new \InvalidArgumentException('You are already have invitation from this user');
        }

        return $user;
    }

    /**
     * @param User         $user
     * @param Connection[] $connections
     *
     * @return array
     */
    public function normalizeResults(User $user, array $connections)
    {
        $locale = $this
            ->getLocale()
            ->setTimezone($user->getTimezone())
            ->setLocale($user->getLocale());

        $data = array();
        foreach ($connections as $connection) {
            $dateCreated = $connection->getDateCreated();
            $data[] = array(
                'id'                => $connection->getId(),
                'email'             => $connection->getParent()->getEmail(),
                'state'             => $connection->getState(),
                'created'           => $locale->getDateTimeFormatter()->format($dateCreated->getTimestamp()),
                'created_full'      => $locale->getDateTimeFormatter(\IntlDateFormatter::FULL)
                                              ->format($dateCreated->getTimestamp()),
                'created_timestamp' => $dateCreated->getTimestamp(),
            );
        }

        return $data;
    }

    /**
     * @param int $connectionId
     *
     * @return Connection
     */
    public function findById($connectionId)
    {
        return $this->getConnectionRepository()->find($connectionId, null);
    }
}
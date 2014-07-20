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
     * @param User   $currentUser
     *
     * @return User
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getInvitedUser($email, User $currentUser)
    {
        $isValidEmail = $this->getEmailValidator()->isValid($email);
        if (!$isValidEmail) {
            throw new \InvalidArgumentException('Email is not valid');
        }

        $user = $this->getUserData()->findUser($email);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        if ($user->getId() == $currentUser->getId()) {
            throw new \InvalidArgumentException('You cannot invite yourself');
        }

        $criteria = array('parent' => $user, 'user' => $currentUser);
        $connection = $this->getConnectionRepository()->findOneBy($criteria);
        if ($connection !== null) {
            throw new \InvalidArgumentException('User is already invited');
        }

        $criteria = array('user' => $user, 'parent' => $currentUser);
        $connection = $this->getConnectionRepository()->findOneBy($criteria);
        if ($connection !== null) {
            throw new \InvalidArgumentException('You already have invitation from this user');
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
                'parent'            => $connection->getUser()->getEmail(),
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
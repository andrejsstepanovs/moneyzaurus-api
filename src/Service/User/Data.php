<?php

namespace Api\Service\User;

use Doctrine\ORM\EntityRepository;
use Api\Entities\User;
use Api\Service\AccessorTrait;

/**
 * Class Data
 *
 * @package Api\Service\User
 *
 * @method Data             setUser(EntityRepository $user)
 * @method EntityRepository getUser()
 */
class Data
{
    use AccessorTrait;

    /**
     * @param string $username
     *
     * @return null|User
     */
    public function findUser($username)
    {
        $criteria = array('email' => $username);

        return $this->getUser()->findOneBy($criteria);
    }

}

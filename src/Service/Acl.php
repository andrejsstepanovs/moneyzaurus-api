<?php

namespace Api\Service;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Api\Entities\User;

/**
 * Class Acl
 *
 * @package Api\Service
 */
class Acl
{
    const ROLE_INDEX        = '';
    const ROLE_AUTHENTICATE = 'authenticate';
    const ROLE_TRANSACTIONS = 'transactions';
    const ROLE_DISTINCT     = 'distinct';
    const ROLE_PREDICT      = 'predict';
    const ROLE_USER         = 'user';
    const ROLE_CONNECTION   = 'connection';

    /** @var ZendAcl */
    private $acl;

    /** @var bool */
    private $initialized = false;

    /**
     * @param ZendAcl $acl
     *
     * @return $this
     */
    public function setAcl(ZendAcl $acl)
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * @return ZendAcl
     */
    private function getAcl()
    {
        return $this->acl;
    }

    /**
     * @return $this
     */
    private function initRoles()
    {
        $admin = new Role(User::ROLE_ADMIN);
        $user  = new Role(User::ROLE_USER);
        $guest = new Role(User::ROLE_GUEST);

        $this->getAcl()->addRole($guest);
        $this->getAcl()->addRole($user, $guest);
        $this->getAcl()->addRole($admin, $user);

        return $this;
    }

    /**
     * @return $this
     */
    private function initResources()
    {
        $this->getAcl()->addResource(new Resource(self::ROLE_INDEX));
        $this->getAcl()->addResource(new Resource(self::ROLE_AUTHENTICATE));
        $this->getAcl()->addResource(new Resource(self::ROLE_TRANSACTIONS));
        $this->getAcl()->addResource(new Resource(self::ROLE_DISTINCT));
        $this->getAcl()->addResource(new Resource(self::ROLE_PREDICT));
        $this->getAcl()->addResource(new Resource(self::ROLE_USER));
        $this->getAcl()->addResource(new Resource(self::ROLE_CONNECTION));

        return $this;
    }

    /**
     * @return $this
     */
    private function initPrivileges()
    {
        $this->getAcl()->allow(User::ROLE_GUEST, self::ROLE_INDEX);
        $this->getAcl()->allow(User::ROLE_GUEST, self::ROLE_AUTHENTICATE, array('login', 'password-recovery'));

        $this->getAcl()->deny(User::ROLE_USER, self::ROLE_AUTHENTICATE, array('login', 'password-recovery'));
        $this->getAcl()->allow(User::ROLE_USER, self::ROLE_AUTHENTICATE, array('logout'));
        $this->getAcl()->allow(User::ROLE_USER, self::ROLE_TRANSACTIONS);
        $this->getAcl()->allow(User::ROLE_USER, self::ROLE_DISTINCT);
        $this->getAcl()->allow(User::ROLE_USER, self::ROLE_PREDICT);
        $this->getAcl()->allow(User::ROLE_USER, self::ROLE_USER);
        $this->getAcl()->allow(User::ROLE_USER, self::ROLE_CONNECTION);

        $this->getAcl()->deny(User::ROLE_ADMIN, self::ROLE_AUTHENTICATE, array('login', 'password-recovery'));
        $this->getAcl()->allow(User::ROLE_ADMIN);

        return $this;
    }

    /**
     * @return $this
     */
    private function initialize()
    {
        if (!$this->initialized) {
            $this->initialized = true;
            $this->initRoles();
            $this->initResources();
            $this->initPrivileges();
        }

        return $this;
    }

    /**
     * @param string $role
     * @param string $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege = null)
    {
        $this->initialize();

        return (bool)$this->getAcl()->isAllowed($role, $resource, $privilege);
    }
}
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
    const RESOURCE_INDEX        = '';
    const RESOURCE_AUTHENTICATE = 'authenticate';
    const RESOURCE_TRANSACTIONS = 'transactions';
    const RESOURCE_DISTINCT     = 'distinct';
    const RESOURCE_PREDICT      = 'predict';
    const RESOURCE_USER         = 'user';
    const RESOURCE_CONNECTION   = 'connection';
    const RESOURCE_CHART        = 'chart';

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
        $this->getAcl()->addResource(new Resource(self::RESOURCE_INDEX));
        $this->getAcl()->addResource(new Resource(self::RESOURCE_AUTHENTICATE));
        $this->getAcl()->addResource(new Resource(self::RESOURCE_TRANSACTIONS));
        $this->getAcl()->addResource(new Resource(self::RESOURCE_DISTINCT));
        $this->getAcl()->addResource(new Resource(self::RESOURCE_PREDICT));
        $this->getAcl()->addResource(new Resource(self::RESOURCE_USER));
        $this->getAcl()->addResource(new Resource(self::RESOURCE_CONNECTION));
        $this->getAcl()->addResource(new Resource(self::RESOURCE_CHART));

        return $this;
    }

    /**
     * @return $this
     */
    private function initPrivileges()
    {
        $this->getAcl()->allow(User::ROLE_GUEST, self::RESOURCE_INDEX);
        $this->getAcl()->allow(User::ROLE_GUEST, self::RESOURCE_AUTHENTICATE, array('login', 'password-recovery'));
        $this->getAcl()->allow(User::ROLE_GUEST, self::RESOURCE_USER, array('register'));

        $this->getAcl()->deny(User::ROLE_USER, self::RESOURCE_AUTHENTICATE, array('login', 'password-recovery'));
        $this->getAcl()->allow(User::ROLE_USER, self::RESOURCE_AUTHENTICATE, array('logout'));
        $this->getAcl()->allow(User::ROLE_USER, self::RESOURCE_TRANSACTIONS);
        $this->getAcl()->allow(User::ROLE_USER, self::RESOURCE_DISTINCT);
        $this->getAcl()->allow(User::ROLE_USER, self::RESOURCE_PREDICT);
        $this->getAcl()->allow(User::ROLE_USER, self::RESOURCE_USER);
        $this->getAcl()->deny(User::ROLE_USER, self::RESOURCE_USER, array('register'));
        $this->getAcl()->allow(User::ROLE_USER, self::RESOURCE_CONNECTION);
        $this->getAcl()->allow(User::ROLE_USER, self::RESOURCE_CHART);

        $this->getAcl()->deny(User::ROLE_ADMIN, self::RESOURCE_AUTHENTICATE, array('login', 'password-recovery'));
        $this->getAcl()->deny(User::ROLE_ADMIN, self::RESOURCE_USER, array('register'));
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
<?php

namespace Tests\Service;

use Api\Service\Acl;
use Api\Entities\User;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Tests\TestCase;

/**
 * Class AclTest
 *
 * @package Tests
 */
class AclTest extends TestCase
{
    /** @var Acl */
    private $sut;

    /** @var ZendAcl */
    private $acl;

    public function setUp()
    {
        $this->sut = new Acl();
        $this->sut->setAcl($this->getZendAcl());
    }

    /**
     * @return ZendAcl
     */
    private function getZendAcl()
    {
        if ($this->acl === null) {
            $this->acl = new ZendAcl();
        }

        return $this->acl;
    }

    /**
     * @return array
     */
    public function allowedDataProvider()
    {
        return array(
            array(User::ROLE_GUEST, Acl::RESOURCE_INDEX, null, true),
            array(User::ROLE_GUEST, Acl::RESOURCE_AUTHENTICATE, 'login', true),
            array(User::ROLE_GUEST, Acl::RESOURCE_AUTHENTICATE, 'logout', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_AUTHENTICATE, 'password-recovery', true),
            array(User::ROLE_GUEST, Acl::RESOURCE_TRANSACTIONS, null, false),
            array(User::ROLE_GUEST, Acl::RESOURCE_TRANSACTIONS, 'id', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_TRANSACTIONS, 'list', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_TRANSACTIONS, 'create', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_TRANSACTIONS, 'remove', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_TRANSACTIONS, 'update', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_DISTINCT, 'groups', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_PREDICT, 'group', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_USER, 'update', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_USER, 'data', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_USER, 'register', true),
            array(User::ROLE_GUEST, Acl::RESOURCE_CONNECTION, null, false),
            array(User::ROLE_GUEST, Acl::RESOURCE_CONNECTION, 'list', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_CONNECTION, 'add', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_CONNECTION, 'accept', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_CONNECTION, 'reject', false),
            array(User::ROLE_GUEST, Acl::RESOURCE_CHART, null, false),
            array(User::ROLE_GUEST, Acl::RESOURCE_CHART, 'pie', false),

            array(User::ROLE_USER, Acl::RESOURCE_INDEX, null, true),
            array(User::ROLE_USER, Acl::RESOURCE_AUTHENTICATE, 'login', false),
            array(User::ROLE_USER, Acl::RESOURCE_AUTHENTICATE, 'logout', true),
            array(User::ROLE_USER, Acl::RESOURCE_AUTHENTICATE, 'password-recovery', false),
            array(User::ROLE_USER, Acl::RESOURCE_TRANSACTIONS, null, true),
            array(User::ROLE_USER, Acl::RESOURCE_TRANSACTIONS, 'id', true),
            array(User::ROLE_USER, Acl::RESOURCE_TRANSACTIONS, 'list', true),
            array(User::ROLE_USER, Acl::RESOURCE_TRANSACTIONS, 'create', true),
            array(User::ROLE_USER, Acl::RESOURCE_TRANSACTIONS, 'remove', true),
            array(User::ROLE_USER, Acl::RESOURCE_TRANSACTIONS, 'update', true),
            array(User::ROLE_USER, Acl::RESOURCE_DISTINCT, 'groups', true),
            array(User::ROLE_USER, Acl::RESOURCE_PREDICT, 'group', true),
            array(User::ROLE_USER, Acl::RESOURCE_USER, 'update', true),
            array(User::ROLE_USER, Acl::RESOURCE_USER, 'data', true),
            array(User::ROLE_USER, Acl::RESOURCE_USER, 'register', false),
            array(User::ROLE_USER, Acl::RESOURCE_CONNECTION, null, true),
            array(User::ROLE_USER, Acl::RESOURCE_CONNECTION, 'list', true),
            array(User::ROLE_USER, Acl::RESOURCE_CONNECTION, 'add', true),
            array(User::ROLE_USER, Acl::RESOURCE_CONNECTION, 'accept', true),
            array(User::ROLE_USER, Acl::RESOURCE_CONNECTION, 'reject', true),
            array(User::ROLE_USER, Acl::RESOURCE_CONNECTION, 'reject', true),
            array(User::ROLE_USER, Acl::RESOURCE_CHART, null, true),
            array(User::ROLE_USER, Acl::RESOURCE_CHART, 'pie', true),

            array(User::ROLE_ADMIN, Acl::RESOURCE_INDEX, null, true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_AUTHENTICATE, 'login', false),
            array(User::ROLE_ADMIN, Acl::RESOURCE_AUTHENTICATE, 'logout', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_AUTHENTICATE, 'password-recovery', false),
            array(User::ROLE_ADMIN, Acl::RESOURCE_TRANSACTIONS, null, true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_TRANSACTIONS, '', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_TRANSACTIONS, 'id', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_TRANSACTIONS, 'list', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_TRANSACTIONS, 'create', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_TRANSACTIONS, 'remove', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_TRANSACTIONS, 'update', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_DISTINCT, 'groups', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_PREDICT, 'group', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_USER, 'update', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_USER, 'data', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_USER, 'register', false),
            array(User::ROLE_ADMIN, Acl::RESOURCE_CONNECTION, null, true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_CONNECTION, 'list', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_CONNECTION, 'add', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_CONNECTION, 'accept', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_CONNECTION, 'reject', true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_CHART, null, true),
            array(User::ROLE_ADMIN, Acl::RESOURCE_CHART, 'pie', true),
        );
    }

    /**
     * @dataProvider allowedDataProvider
     *
     * @param string $userRole
     * @param string $resource
     * @param string $privilege
     * @param bool   $expected
     */
    public function testAcl($userRole, $resource, $privilege, $expected)
    {
        $allowed = $this->sut->isAllowed($userRole, $resource, $privilege);
        $this->assertEquals($expected, $allowed);
    }

}
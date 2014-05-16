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
            array(User::ROLE_GUEST, Acl::ROLE_INDEX, null, true),
            array(User::ROLE_GUEST, Acl::ROLE_AUTHENTICATE, 'login', true),
            array(User::ROLE_GUEST, Acl::ROLE_AUTHENTICATE, 'logout', false),
            array(User::ROLE_GUEST, Acl::ROLE_AUTHENTICATE, 'password-recovery', true),
            array(User::ROLE_GUEST, Acl::ROLE_TRANSACTIONS, null, false),
            array(User::ROLE_GUEST, Acl::ROLE_TRANSACTIONS, 'id', false),
            array(User::ROLE_GUEST, Acl::ROLE_TRANSACTIONS, 'list', false),
            array(User::ROLE_GUEST, Acl::ROLE_TRANSACTIONS, 'create', false),
            array(User::ROLE_GUEST, Acl::ROLE_TRANSACTIONS, 'remove', false),
            array(User::ROLE_GUEST, Acl::ROLE_TRANSACTIONS, 'update', false),
            array(User::ROLE_GUEST, Acl::ROLE_DISTINCT, 'groups', false),
            array(User::ROLE_GUEST, Acl::ROLE_PREDICT, 'group', false),
            array(User::ROLE_GUEST, Acl::ROLE_USER, 'data', false),
            array(User::ROLE_GUEST, Acl::ROLE_CONNECTION, null, false),
            array(User::ROLE_GUEST, Acl::ROLE_CONNECTION, 'list', false),
            array(User::ROLE_GUEST, Acl::ROLE_CONNECTION, 'add', false),
            array(User::ROLE_GUEST, Acl::ROLE_CONNECTION, 'accept', false),
            array(User::ROLE_GUEST, Acl::ROLE_CONNECTION, 'reject', false),

            array(User::ROLE_USER, Acl::ROLE_INDEX, null, true),
            array(User::ROLE_USER, Acl::ROLE_AUTHENTICATE, 'login', false),
            array(User::ROLE_USER, Acl::ROLE_AUTHENTICATE, 'logout', true),
            array(User::ROLE_USER, Acl::ROLE_AUTHENTICATE, 'password-recovery', false),
            array(User::ROLE_USER, Acl::ROLE_TRANSACTIONS, null, true),
            array(User::ROLE_USER, Acl::ROLE_TRANSACTIONS, 'id', true),
            array(User::ROLE_USER, Acl::ROLE_TRANSACTIONS, 'list', true),
            array(User::ROLE_USER, Acl::ROLE_TRANSACTIONS, 'create', true),
            array(User::ROLE_USER, Acl::ROLE_TRANSACTIONS, 'remove', true),
            array(User::ROLE_USER, Acl::ROLE_TRANSACTIONS, 'update', true),
            array(User::ROLE_USER, Acl::ROLE_DISTINCT, 'groups', true),
            array(User::ROLE_USER, Acl::ROLE_PREDICT, 'group', true),
            array(User::ROLE_USER, Acl::ROLE_USER, 'data', true),
            array(User::ROLE_USER, Acl::ROLE_CONNECTION, null, true),
            array(User::ROLE_USER, Acl::ROLE_CONNECTION, 'list', true),
            array(User::ROLE_USER, Acl::ROLE_CONNECTION, 'add', true),
            array(User::ROLE_USER, Acl::ROLE_CONNECTION, 'accept', true),
            array(User::ROLE_USER, Acl::ROLE_CONNECTION, 'reject', true),

            array(User::ROLE_ADMIN, Acl::ROLE_INDEX, null, true),
            array(User::ROLE_ADMIN, Acl::ROLE_AUTHENTICATE, 'login', false),
            array(User::ROLE_ADMIN, Acl::ROLE_AUTHENTICATE, 'logout', true),
            array(User::ROLE_ADMIN, Acl::ROLE_AUTHENTICATE, 'password-recovery', false),
            array(User::ROLE_ADMIN, Acl::ROLE_TRANSACTIONS, null, true),
            array(User::ROLE_ADMIN, Acl::ROLE_TRANSACTIONS, '', true),
            array(User::ROLE_ADMIN, Acl::ROLE_TRANSACTIONS, 'id', true),
            array(User::ROLE_ADMIN, Acl::ROLE_TRANSACTIONS, 'list', true),
            array(User::ROLE_ADMIN, Acl::ROLE_TRANSACTIONS, 'create', true),
            array(User::ROLE_ADMIN, Acl::ROLE_TRANSACTIONS, 'remove', true),
            array(User::ROLE_ADMIN, Acl::ROLE_TRANSACTIONS, 'update', true),
            array(User::ROLE_ADMIN, Acl::ROLE_DISTINCT, 'groups', true),
            array(User::ROLE_ADMIN, Acl::ROLE_PREDICT, 'group', true),
            array(User::ROLE_ADMIN, Acl::ROLE_USER, 'data', true),
            array(User::ROLE_ADMIN, Acl::ROLE_CONNECTION, null, true),
            array(User::ROLE_ADMIN, Acl::ROLE_CONNECTION, 'list', true),
            array(User::ROLE_ADMIN, Acl::ROLE_CONNECTION, 'add', true),
            array(User::ROLE_ADMIN, Acl::ROLE_CONNECTION, 'accept', true),
            array(User::ROLE_ADMIN, Acl::ROLE_CONNECTION, 'reject', true),
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
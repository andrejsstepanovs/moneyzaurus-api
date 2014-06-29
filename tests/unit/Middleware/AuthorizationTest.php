<?php

namespace Tests\Middleware;

use Api\Middleware\Authorization;
use Tests\TestCase;

/**
 * Class AuthorizationTest
 *
 * @package Tests
 */
class AuthorizationTest extends TestCase
{
    /** @var Authorization */
    private $sut;

    public function setUp()
    {
        $this->sut = new Authorization();
        $this->sut->setAcl($this->mock()->get('Api\Service\Acl'));
        $this->sut->setApplication($this->mock()->get('\Api\Slim'));
        $this->sut->setNextMiddleware($this->mock()->get('\Slim\Middleware'));
        $this->sut->setToken($this->mock()->get('Api\Service\Authorization\Token'));
        $this->sut->setTime($this->mock()->get('\Api\Service\Time'));
        $this->sut->setJsonMiddleware($this->mock()->get('Api\Middleware\Json'));

        $this->mock()->get('\Api\Slim')
             ->expects($this->any())
             ->method('request')
             ->will($this->returnValue($this->mock()->get('\Slim\Http\Request')));

        $this->mock()->get('\Api\Slim')
             ->expects($this->any())
             ->method('response')
             ->will($this->returnValue($this->mock()->get('\Slim\Http\Response')));

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->any())
             ->method('setTimezone')
             ->will($this->returnSelf());

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->any())
             ->method('getDateTime')
             ->will($this->returnValue(new \DateTime()));
    }

    public function testTokenIsNotProvidedAndAccessNotAllowed()
    {
        $token     = null;
        $resource  = 'requestResource';
        $privilege = 'requestPrivilege';
        $separator = '/';
        $path      = $resource . $separator . $privilege;

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('post')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('getPath')
             ->will($this->returnValue($path));

        $this->mock()->get('Api\Service\Acl')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(false));

        $this->mock()->get('\Slim\Http\Response')
             ->expects($this->once())
             ->method('setStatus')
             ->with($this->equalTo(403));

        $this->mock()->get('Api\Middleware\Json')
             ->expects($this->once())
             ->method('modifyResponse');

        $this->sut->call();
    }

    public function testTokenIsNotProvidedAndAccessIsAllowed()
    {
        $token     = null;
        $resource  = 'requestResource';
        $privilege = 'requestPrivilege';
        $separator = '/';
        $path      = $resource . $separator . $privilege;

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('post')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('getPath')
             ->will($this->returnValue($path));

        $this->mock()->get('Api\Service\Acl')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->sut->call();
    }

    public function testResourceNotAllowed()
    {
        $token = 'TOKEN VALUE';
        $resource = 'requestResource';
        $privilege = 'requestPrivilege';
        $separator = '/';
        $path  = $resource . $separator . $privilege;

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('getPath')
             ->will($this->returnValue($path));

        $this->mock()->get('Api\Service\Acl')
            ->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $this->mock()->get('\Slim\Http\Response')
             ->expects($this->once())
             ->method('setStatus')
             ->with($this->equalTo(403));

        $this->mock()->get('Api\Middleware\Json')
             ->expects($this->once())
             ->method('modifyResponse');

        $this->sut->call();
    }

    public function testUserFoundButResourceNotAllowed()
    {
        $token = 'TOKEN VALUE';
        $resource = 'requestResource';
        $privilege = 'requestPrivilege';
        $separator = '/';
        $path  = $resource . $separator . $privilege;
        $role = 'user';

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('getPath')
             ->will($this->returnValue($path));

        $this->mock()->get('Api\Service\Authorization\Token')
            ->expects($this->once())
            ->method('findAccessToken')
            ->will($this->returnValue($this->mock()->get('Api\Entities\AccessToken')));

        $this->mock()->get('Api\Entities\AccessToken')
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Entities\User')
            ->expects($this->once())
            ->method('getRole')
            ->will($this->returnValue($role));

        $this->mock()->get('Api\Service\Acl')
            ->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $this->mock()->get('\Slim\Http\Response')
             ->expects($this->once())
             ->method('setStatus')
             ->with($this->equalTo(403));

        $this->sut->call();
    }

    public function testUserNotFoundButResourceAllowed()
    {
        $token = 'TOKEN VALUE';
        $resource = 'requestResource';
        $privilege = 'requestPrivilege';
        $separator = '/';
        $path  = $resource . $separator . $privilege;

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('getPath')
             ->will($this->returnValue($path));

        $this->mock()->get('Api\Service\Acl')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('\Api\Slim')
            ->expects($this->exactly(2))
            ->method('config');

        $this->mock()->get('\Slim\Middleware')
            ->expects($this->once())
            ->method('call');

        $this->sut->call();
    }

    public function testUserFoundAndIsAllowedWillFetchConnectedUsers()
    {
        $token = 'TOKEN VALUE';
        $resource = 'requestResource';
        $privilege = 'requestPrivilege';
        $separator = '/';
        $path  = $resource . $separator . $privilege;
        $role = 'user';
        $connectedUserIds = [1, 2, 3];

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo('token'))
             ->will($this->returnValue($token));

        $this->mock()->get('\Slim\Http\Request')
             ->expects($this->once())
             ->method('getPath')
             ->will($this->returnValue($path));

        $this->mock()->get('Api\Service\Authorization\Token')
             ->expects($this->once())
             ->method('findAccessToken')
             ->will($this->returnValue($this->mock()->get('Api\Entities\AccessToken')));

        $this->mock()->get('Api\Entities\AccessToken')
             ->expects($this->once())
             ->method('getUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Service\Authorization\Token')
             ->expects($this->once())
             ->method('getConnectedUsers')
             ->will($this->returnValue($connectedUserIds));

        $this->mock()->get('Api\Entities\User')
             ->expects($this->once())
             ->method('getRole')
             ->will($this->returnValue($role));

        $this->mock()->get('Api\Entities\User')
             ->expects($this->once())
             ->method('getTimezone')
             ->will($this->returnValue('Europe/Berlin'));

        $this->mock()->get('Api\Service\Acl')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('\Api\Slim')
            ->expects($this->at(3))
            ->method('config')
            ->with($this->equalTo('connectedUserIds'), $this->equalTo($connectedUserIds));

        $this->mock()->get('\Slim\Middleware')
             ->expects($this->once())
             ->method('call');


        $this->sut->call();
    }

}
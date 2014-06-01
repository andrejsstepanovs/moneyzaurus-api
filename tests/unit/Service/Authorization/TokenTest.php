<?php

namespace Tests\Service\Authorization;

use Api\Service\Authorization\Token;
use Tests\TestCase;

/**
 * Class TokenTest
 *
 * @package Tests
 */
class TokenTest extends TestCase
{
    /** @var Token */
    private $sut;

    public function setUp()
    {
        $this->sut = new Token();
        $this->sut->setEntityManager($this->mock()->get('Doctrine\ORM\EntityManager'));
        $this->sut->setAccessToken($this->mock()->get('Api\Entities\AccessToken'));
    }

    public function testGetToken()
    {
        $this->mock()->get('Api\Entities\AccessToken')
            ->expects($this->once())
            ->method('setToken')
            ->with($this->isType('string'))
            ->will($this->returnSelf());

        $this->mock()->get('Api\Entities\AccessToken')
            ->expects($this->once())
            ->method('setUser')
            ->with($this->isInstanceOf(get_class($this->mock()->get('Api\Entities\User'))))
            ->will($this->returnSelf());

        $this->mock()->get('Api\Entities\AccessToken')
            ->expects($this->once())
            ->method('setCreated')
            ->with($this->isInstanceOf('DateTime'))
            ->will($this->returnSelf());

        $token = $this->sut->get($this->mock()->get('Api\Entities\User'));

        $this->assertInstanceOf('Api\Entities\AccessToken', $token);
    }

    public function testFindUserIfExistsWillReturnUser()
    {
        $token = 'token1';

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->mock()->get('Doctrine\ORM\EntityRepository')));

        $this->mock()->get('Doctrine\ORM\EntityRepository')
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('token' => $token)))
            ->will($this->returnValue($this->mock()->get('Api\Entities\AccessToken')));

        $this->mock()->get('Api\Entities\AccessToken')
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $response = $this->sut->findUser($token);

        $this->assertInstanceOf(get_class($this->mock()->get('Api\Entities\User')), $response);
    }

    public function testFindUserIfNotExistsWillReturnNull()
    {
        $token = 'token1';

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('getRepository')
             ->will($this->returnValue($this->mock()->get('Doctrine\ORM\EntityRepository')));

        $this->mock()->get('Doctrine\ORM\EntityRepository')
             ->expects($this->once())
             ->method('findOneBy')
             ->with($this->equalTo(array('token' => $token)))
             ->will($this->returnValue(null));

        $response = $this->sut->findUser($token);

        $this->assertNull($response);
    }

    public function testGetConnectedUsersIfUserNotFoundWillReturnArray()
    {
        $token = 'TOKEN';

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('getRepository')
             ->will($this->returnValue($this->mock()->get('Doctrine\ORM\EntityRepository')));

        $this->mock()->get('Doctrine\ORM\EntityRepository')
             ->expects($this->once())
             ->method('findOneBy')
             ->with($this->equalTo(array('token' => $token)))
             ->will($this->returnValue(null));

        $this->sut->findUser($token);

        $response = $this->sut->getConnectedUsers();
        $this->assertEquals($response, array());
    }

    public function testGetConnectedUsersA()
    {
        $this->mock()->get('Api\Entities\User')
            ->expects($this->at(0))
            ->method('getId')
            ->will($this->returnValue(1));

       $this->mock()->get('Api\Entities\User')
            ->expects($this->at(1))
            ->method('getId')
            ->will($this->returnValue(2));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('getRepository')
             ->will($this->returnValue($this->mock()->get('Doctrine\ORM\EntityRepository')));

        $this->mock()->get('Doctrine\ORM\EntityRepository')
             ->expects($this->once())
             ->method('findBy')
             ->with($this->equalTo(array('user' => 1, 'state' => 'accepted')))
             ->will($this->returnValue(array($this->mock()->get('Api\Entities\Connection'))));

        $this->mock()->get('Api\Entities\Connection')
            ->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->sut->setUser($this->mock()->get('Api\Entities\User'));
        $response = $this->sut->getConnectedUsers();

        $this->assertEquals(array(2), $response);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRemoveAccessTokenNotFoundWillThrowException()
    {
        $token = 'token';

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('getRepository')
             ->will($this->returnValue($this->mock()->get('Doctrine\ORM\EntityRepository')));

        $this->mock()->get('Doctrine\ORM\EntityRepository')
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $response = $this->sut->remove($this->mock()->get('Api\Entities\User'), $token);

        $this->assertTrue($response);
    }

    public function testSuccessfulRemove()
    {
        $token = 'token';

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('getRepository')
             ->will($this->returnValue($this->mock()->get('Doctrine\ORM\EntityRepository')));

        $this->mock()->get('Doctrine\ORM\EntityRepository')
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->mock()->get('Api\Entities\AccessToken')));

        $response = $this->sut->remove($this->mock()->get('Api\Entities\User'), $token);

        $this->assertTrue($response);
    }

    public function testRemoveNotSavedAndRolledBackWillReturnFalse()
    {
        $token = 'token';

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('getRepository')
             ->will($this->returnValue($this->mock()->get('Doctrine\ORM\EntityRepository')));

        $this->mock()->get('Doctrine\ORM\EntityRepository')
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->mock()->get('Api\Entities\AccessToken')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('commit')
            ->will($this->throwException(new \RuntimeException('TEST')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('rollback');

        $response = $this->sut->remove($this->mock()->get('Api\Entities\User'), $token);

        $this->assertFalse($response);
    }

}
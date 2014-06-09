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
        $this->sut->setTime($this->mock()->get('\Api\Service\Time'));
        $this->sut->setTokenInterval('P1Y');

        $this->mock()->get('\Api\Service\Time')
            ->expects($this->any())
            ->method('setTimezone')
            ->will($this->returnSelf());
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
            ->method('setUsedAt')
            ->with($this->equalTo(null))
            ->will($this->returnSelf());

        $this->mock()->get('Api\Entities\AccessToken')
            ->expects($this->once())
            ->method('setValidUntil')
            ->will($this->returnSelf());

        $this->mock()->get('Api\Entities\AccessToken')
            ->expects($this->once())
            ->method('setCreated')
            ->with($this->isInstanceOf('DateTime'))
            ->will($this->returnSelf());

        $token = $this->sut->get($this->mock()->get('Api\Entities\User'));

        $this->assertInstanceOf('Api\Entities\AccessToken', $token);
    }

    public function testFindAccessToken()
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

        $response = $this->sut->findAccessToken($token);

        $this->assertInstanceOf(get_class($this->mock()->get('Api\Entities\AccessToken')), $response);
    }

    /**
     * @expectedException \Api\Service\Exception\TokenExpiredException
     */
    public function testValidateExpired()
    {
        $token = $this->mock()->get('Api\Entities\AccessToken');
        $user  = $this->mock()->get('Api\Entities\User');

        $this->mock()->get('Api\Entities\User')
             ->expects($this->once())
             ->method('getTimezone')
             ->will($this->returnValue('Europe/Berlin'));

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->once())
             ->method('compareDateTime')
             ->will($this->returnValue(false));

        $this->mock()->get('Api\Entities\AccessToken')
             ->expects($this->once())
             ->method('getValidUntil')
             ->will($this->returnValue(new \DateTime()));

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->once())
             ->method('getDateTime')
             ->will($this->returnValue(new \DateTime()));

        $this->sut->validateExpired($token, $user);
    }

    public function testGetConnectedUsers()
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

        $response = $this->sut->getConnectedUsers($this->mock()->get('Api\Entities\User'));

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

    /**
     * @return array
     */
    public function intervalDataProvider()
    {
        return array(
            array(new \DateTime('2010-01-01 00:00:00'), '2011-01-01 00:00:00'),
            array(new \DateTime('2011-04-01 15:00:00'), '2012-04-01 15:00:00'),
            array(new \DateTime('2011-04-21 10:05:10'), '2012-04-21 10:05:10'),
        );
    }

    /**
     * @dataProvider intervalDataProvider
     *
     * @param \DateTime $dateTime
     * @param string    $expected
     */
    public function testGetInterval($dateTime, $expected)
    {
        $response = $this->sut->getInterval($dateTime);

        $formatted = $response->format('Y-m-d H:i:s');

        $this->assertEquals($expected, $formatted);
    }
}
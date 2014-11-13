<?php

namespace Tests\Controller\Connection;

use Api\Controller\Connection\AcceptController;
use Api\Entities\Connection;
use Tests\TestCase;

/**
 * Class AcceptControllerTest
 *
 * @package Tests
 */
class AcceptControllerTest extends TestCase
{
    /** @var AcceptController */
    private $sut;

    public function setUp()
    {
        $this->sut = new AcceptController();
        $this->sut->setConnectionData($this->mock()->get('Api\Service\Connection\Data'));
        $this->sut->setConnectionSave($this->mock()->get('Api\Service\Connection\Save'));
    }

    public function testConnectionNotFound()
    {
        $connectionId = 1;

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectionId);

        $this->assertFalse($response['success']);
        $this->assertEquals('Connection not found', $response['message']);
    }

    public function testWrongConnection()
    {
        $connectionId = 1;
        $user   = $this->mock()->get('Api\Entities\User');
        $parent = clone $user;

        $parent->expects($this->once())->method('getId')->will($this->returnValue(2));

        $this->mock()->get('Api\Service\Connection\Data')
            ->expects($this->once())
            ->method('findById')
            ->will($this->returnValue($this->mock()->get('Api\Entities\Connection')));

        $this->mock()->get('Api\Entities\Connection')
            ->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parent));

        $response = $this->sut->getResponse($user, $connectionId);

        $this->assertFalse($response['success']);
        $this->assertEquals('Connection cannot be accepted', $response['message']);
    }

    public function testConnectionHaveWrongStatus()
    {
        $connectionId = 1;
        $user   = $this->mock()->get('Api\Entities\User');

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('findById')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Connection')));

        $this->mock()->get('Api\Entities\Connection')
             ->expects($this->any())
             ->method('getParent')
             ->will($this->returnValue($user));

        $this->mock()->get('Api\Entities\Connection')
             ->expects($this->any())
             ->method('getState')
             ->will($this->returnValue(Connection::STATE_ACCEPTED));

        $response = $this->sut->getResponse($user, $connectionId);

        $this->assertFalse($response['success']);
        $this->assertEquals('Connection already accepted', $response['message']);
    }

    public function testSaveThrowsException()
    {
        $connectionId = 1;
        $user = $this->mock()->get('Api\Entities\User');

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('findById')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Connection')));

        $this->mock()->get('Api\Entities\Connection')
             ->expects($this->any())
             ->method('getParent')
             ->will($this->returnValue($user));

        $this->mock()->get('Api\Service\Connection\Save')
            ->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \InvalidArgumentException('TEST')));

        $response = $this->sut->getResponse($user, $connectionId);

        $this->assertFalse($response['success']);
        $this->assertEquals('TEST', $response['message']);
    }

    public function testSuccessfulSaveWillReturnTrue()
    {
        $connectionId = 1;
        $user   = $this->mock()->get('Api\Entities\User');

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('findById')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Connection')));

        $this->mock()->get('Api\Entities\Connection')
             ->expects($this->any())
             ->method('getParent')
             ->will($this->returnValue($user));

        $response = $this->sut->getResponse($user, $connectionId);

        $this->assertTrue($response['success']);
    }
}

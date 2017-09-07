<?php

namespace Tests\Controller\Connection;

use Api\Controller\Connection\RejectController;
use Api\Entities\Connection;
use Tests\TestCase;

/**
 * Class AcceptControllerTest
 *
 * @package Tests
 */
class RejectControllerTest extends TestCase
{
    /** @var RejectController */
    private $sut;

    public function setUp()
    {
        $this->sut = new RejectController();
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
        $this->assertEquals('Connection cannot be rejected', $response['message']);
    }

    public function testConnectionHaveWrongStatus()
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

        $this->mock()->get('Api\Entities\Connection')
             ->expects($this->any())
             ->method('getState')
             ->will($this->returnValue(Connection::STATE_REJECTED));

        $response = $this->sut->getResponse($user, $connectionId);

        $this->assertFalse($response['success']);
        $this->assertEquals('Connection already rejected', $response['message']);
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

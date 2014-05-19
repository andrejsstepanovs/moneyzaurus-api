<?php

namespace Tests\Controller\Connection;

use Api\Controller\Connection\AddController;
use Tests\TestCase;

/**
 * Class AddControllerTest
 *
 * @package Tests
 */
class AddControllerTest extends TestCase
{
    /** @var AddController */
    private $sut;

    public function setUp()
    {
        $connection = $this->mock()->get('Api\Entities\Connection');
        $connection->expects($this->any())->method('setUser')->will($this->returnSelf());
        $connection->expects($this->any())->method('setParent')->will($this->returnSelf());
        $connection->expects($this->any())->method('setDateCreated')->will($this->returnSelf());

        $message = $this->mock()->get('Api\Service\Email\Messages\ConnectionInvitation');
        $message->expects($this->any())->method('setConnection')->will($this->returnSelf());


        $this->sut = new AddController();
        $this->sut->setConnectionSave($this->mock()->get('Api\Service\Connection\Save'));
        $this->sut->setConnectionData($this->mock()->get('Api\Service\Connection\Data'));
        $this->sut->setConnection($connection);
        $this->sut->setMessage($message);
        $this->sut->setMailer($this->mock()->get('\Swift_Mailer'));
    }

    public function testAddNotValidEmailWillReturnFalse()
    {
        $email = 'email@email.com';

        $this->mock()->get('Api\Service\Connection\Data')
            ->expects($this->once())
            ->method('getInvitedUser')
            ->will($this->throwException(new \InvalidArgumentException('TEST')));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $email);

        $this->assertTrue(is_array($response));
        $this->assertFalse($response['success']);
        $this->assertEquals('TEST', $response['message']);
    }

    public function testSavingConnectionThrowsException()
    {
        $email = 'email@email.com';

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('getInvitedUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Service\Connection\Save')
             ->expects($this->once())
             ->method('save')
             ->will($this->throwException(new \InvalidArgumentException('TEST')));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $email);

        $this->assertTrue(is_array($response));
        $this->assertFalse($response['success']);
        $this->assertEquals('TEST', $response['message']);
    }

    public function testSuccessfulAdd()
    {
        $email = 'email@email.com';

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('getInvitedUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Service\Connection\Save')
             ->expects($this->once())
             ->method('save')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Connection')));

        $this->mock()->get('Api\Entities\Connection')
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));


        $sendResponse = 1;
        $message = new \Swift_Message();
        $message->setDescription($sendResponse);

        $this->mock()->get('Api\Service\Email\Messages\ConnectionInvitation')
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $email);

        $this->assertTrue(is_array($response));
        $this->assertTrue($response['success']);
    }
}


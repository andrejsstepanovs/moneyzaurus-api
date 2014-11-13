<?php

namespace Tests\Controller\Authenticate;

use Api\Controller\Authenticate\PasswordRecoveryController;
use Tests\TestCase;

/**
 * Class PasswordRecoveryControllerTest
 *
 * @package Tests
 */
class PasswordRecoveryControllerTest extends TestCase
{
    /** @var PasswordRecoveryController */
    private $sut;

    public function setUp()
    {
        $this->sut = new PasswordRecoveryController();
        $this->sut->setCrypt($this->mock()->get('Api\Service\Authorization\Crypt'));
        $this->sut->setMailer($this->mock()->get('\Swift_Mailer'));
        $this->sut->setMessage($this->mock()->get('Api\Service\Email\Messages\PasswordRecovery'));
        $this->sut->setUserData($this->mock()->get('Api\Service\User\Data'));
        $this->sut->setUserSave($this->mock()->get('Api\Service\User\Save'));
    }

    public function testUserNotFoundWillReturnSuccessFalse()
    {
        $username = 'username';

        $response = $this->sut->getResponse($username);

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testUserFoundButProcessThrowsException()
    {
        $username = 'username';

        $this->mock()->get('Api\Service\User\Data')
            ->expects($this->once())
            ->method('findUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Service\Authorization\Crypt')
            ->expects($this->once())
            ->method('create')
            ->will($this->throwException(new \RuntimeException('TEST')));

        $response = $this->sut->getResponse($username);

        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
    }

    public function testSuccessful()
    {
        $username     = 'username';
        $sendResponse = 1;

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Service\Email\Messages\PasswordRecovery')
            ->expects($this->once())
            ->method('setPassword')
            ->will($this->returnSelf());

        $this->mock()->get('Api\Service\Email\Messages\PasswordRecovery')
            ->expects($this->once())
            ->method('setUser')
            ->will($this->returnSelf());

        $message = new \Swift_Message();
        $message->setDescription($sendResponse);

        $this->mock()->get('Api\Service\Email\Messages\PasswordRecovery')
            ->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($message));

        $response = $this->sut->getResponse($username);

        $this->assertEquals(1, $response['success']);
    }
}

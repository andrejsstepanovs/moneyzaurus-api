<?php

namespace Tests\Controller\Authenticate;

use Api\Controller\Authenticate\LoginController;
use Tests\TestCase;

/**
 * Class LoginControllerTest
 *
 * @package Tests
 */
class LoginControllerTest extends TestCase
{
    /** @var LoginController */
    private $sut;

    public function setUp()
    {
        $this->sut = new LoginController();
        $this->sut->setUserData($this->mock()->get('Api\Service\User\Data'));
        $this->sut->setCrypt($this->mock()->get('Api\Service\Authorization\Crypt'));
        $this->sut->setToken($this->mock()->get('Api\Service\Authorization\Token'));
    }

    public function testUserNotFoundWillReturnFailure()
    {
        $username = 'username';
        $password = 'password';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue(false));

        $response = $this->sut->getResponse($username, $password);

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testUserFoundButPasswordNotValidWillReturnFailure()
    {
        $username = 'username';
        $password = 'password';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Service\Authorization\Crypt')
             ->expects($this->once())
             ->method('verify')
             ->will($this->returnValue(false));

        $response = $this->sut->getResponse($username, $password);

        $this->assertFalse($response['success']);
    }

    public function testUserFoundAndPasswordValid()
    {
        $username = 'username';
        $password = 'password';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Service\Authorization\Crypt')
             ->expects($this->once())
             ->method('verify')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Authorization\Token')
             ->expects($this->once())
             ->method('get')
             ->will($this->returnValue($this->mock()->get('Api\Entities\AccessToken')));

        $response = $this->sut->getResponse($username, $password);

        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertArrayHasKey('id', $response['data']);
        $this->assertArrayHasKey('email', $response['data']);
        $this->assertArrayHasKey('token', $response['data']);
    }

}
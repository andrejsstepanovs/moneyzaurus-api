<?php

namespace Tests\Controller\Authenticate;

use Api\Controller\Authenticate\LogoutController;
use Tests\TestCase;

/**
 * Class LogoutControllerTest
 *
 * @package Tests
 */
class LogoutControllerTest extends TestCase
{
    /** @var LogoutController */
    private $sut;

    public function setUp()
    {
        $this->sut = new LogoutController();
        $this->sut->setToken($this->mock()->get('Api\Service\Authorization\Token'));
    }

    public function testTokenWillReturnFalse()
    {
        $token = 'token';

        $this->mock()->get('Api\Service\Authorization\Token')
             ->expects($this->once())
             ->method('remove')
             ->will($this->returnValue(false));

        $user = $this->mock()->get('Api\Entities\User');

        $response = $this->sut->getResponse($user, $token);

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testTokenWillReturnTrue()
    {
        $token = 'token';

        $this->mock()->get('Api\Service\Authorization\Token')
             ->expects($this->once())
             ->method('remove')
             ->will($this->returnValue(true));

        $user = $this->mock()->get('Api\Entities\User');

        $response = $this->sut->getResponse($user, $token);

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
    }
}

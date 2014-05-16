<?php

namespace Tests\Controller\User;

use Api\Controller\User\DataController;
use Tests\TestCase;

/**
 * Class DataControllerTest
 *
 * @package Tests\Controller\User
 */
class DataControllerTest extends TestCase
{
    /** @var DataController */
    private $sut;

    public function setUp()
    {
        $this->sut = new DataController();
    }

    /**
     * @param string $method
     * @param string $response
     *
     * @return $this
     */
    private function expectedMethod($method, $response)
    {
        $this->mock()->get('Api\Entities\User')
            ->expects($this->once())
            ->method($method)
            ->will($this->returnValue($response));

        return $this;
    }

    public function testGetUserData()
    {
        $userMock = $this->mock()->get('Api\Entities\User');

        $expected = array(
            'id'       => '123',
            'email'    => 'email@test.com',
            'name'     => 'User Name',
            'role'     => 'user',
            'language' => 'en_US',
            'locale'   => 'lv_LV',
            'timezone' => 'Europe/Berlin',
            'state'    => 1
        );

        $this->expectedMethod('getId', $expected['id']);
        $this->expectedMethod('getEmail', $expected['email']);
        $this->expectedMethod('getDisplayName', $expected['name']);
        $this->expectedMethod('getRole', $expected['role']);
        $this->expectedMethod('getLanguage', $expected['language']);
        $this->expectedMethod('getLocale', $expected['locale']);
        $this->expectedMethod('getTimezone', $expected['timezone']);
        $this->expectedMethod('getState', $expected['state']);

        $response = $this->sut->getResponse($userMock);

        $this->assertEquals(
            array(
                'success' => true,
                'data'    => $expected
            ),
            $response
        );
    }
}

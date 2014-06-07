<?php

namespace Tests\Entities;


use Api\Entities\User;
use Tests\TestCase;

/**
 * Class AccessTokenTest
 *
 * @package Tests
 */
class UserTest extends TestCase
{
    /** @var User */
    private $sut;

    public function setUp()
    {
        $this->sut = new User();
    }

    public function testGetDisplayName()
    {
        $value = 'Display Name';
        $response = $this->sut->setDisplayName($value)->getDisplayName();

        $this->assertEquals($value, $response);
    }

    public function testGetEmail()
    {
        $value = 'email@email.com';
        $response = $this->sut->setEmail($value)->getEmail();

        $this->assertEquals($value, $response);
    }

    public function testGetLocale()
    {
        $value = 'lv_LV';
        $response = $this->sut->setLocale($value)->getLocale();

        $this->assertEquals($value, $response);
    }

    public function testGetTimezone()
    {
        $value = 'Europe/Berlin';
        $response = $this->sut->setTimezone($value)->getTimezone();

        $this->assertEquals($value, $response);
    }

    public function testGetLanguage()
    {
        $value = 'lv_LV';
        $response = $this->sut->setLanguage($value)->getLanguage();

        $this->assertEquals($value, $response);
    }

    public function testGetPassword()
    {
        $value = 'password';
        $response = $this->sut->setPassword($value)->getPassword();

        $this->assertEquals($value, $response);
    }

    public function testGetRole()
    {
        $value = 'role';
        $response = $this->sut->setRole($value)->getRole();

        $this->assertEquals($value, $response);
    }

    public function testGetState()
    {
        $value = 1;
        $response = $this->sut->setState($value)->getState();

        $this->assertEquals($value, $response);
    }

    public function testGetId()
    {
        $value = 123;
        $response = $this->sut->setId($value)->getId();

        $this->assertEquals($value, $response);
    }

    public function testGetUsername()
    {
        $value = 'username';
        $response = $this->sut->setUsername($value)->getUsername();

        $this->assertEquals($value, $response);
    }

    public function testGetLoginAttempts()
    {
        $value = 123;
        $response = $this->sut->setLoginAttempts($value)->getLoginAttempts();

        $this->assertEquals($value, $response);
    }

}
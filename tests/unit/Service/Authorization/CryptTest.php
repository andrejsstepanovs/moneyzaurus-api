<?php

namespace Tests\Service\Authorization;

use PHPUnit_Framework_TestCase;
use Api\Service\Authorization\Crypt;
use Tests\TestCase;

/**
 * Class TokenTest
 *
 * @package Tests
 */
class CryptTest extends TestCase
{
    /** @var Crypt */
    private $sut;

    public function setUp()
    {
        $this->sut = new Crypt();
    }

    public function testCryptGetter()
    {
        $response = $this->sut->setCrypt($this->mock()->get('Zend\Crypt\Password\Bcrypt'));
        $this->assertInstanceOf(get_class($this->sut), $response);

        $crypt = $this->sut->getCrypt();
        $this->assertInstanceOf(get_class($this->mock()->get('Zend\Crypt\Password\Bcrypt')), $crypt);
    }

    public function testCreate()
    {
        $password = 'PASSWORD123';
        $hash     = '12345567890';

        $this->mock()->get('Zend\Crypt\Password\Bcrypt')
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo($password))
            ->will($this->returnValue($hash));

        $response = $this->sut->setCrypt($this->mock()->get('Zend\Crypt\Password\Bcrypt'))->create($password);
        $this->assertEquals($hash, $response);
    }

    public function testVerify()
    {
        $password = 'PASSWORD123';
        $hash     = '12345567890';

        $this->mock()->get('Zend\Crypt\Password\Bcrypt')
             ->expects($this->once())
             ->method('verify')
             ->with($this->equalTo($password), $this->equalTo($hash))
             ->will($this->returnValue(true));

        $response = $this->sut->setCrypt($this->mock()->get('Zend\Crypt\Password\Bcrypt'))->verify($password, $hash);
        $this->assertTrue($response);
    }

    public function testGetRandomPasswordWillReturnString()
    {
        $response = $this->sut->getRandomPassword();

        $this->assertInternalType('string', $response);
        $this->assertNotEmpty($response);
    }
}
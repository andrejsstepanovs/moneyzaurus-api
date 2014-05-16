<?php

namespace Tests\Service\Email\Messages;


use Api\Service\Email\Messages\PasswordRecovery;
use Tests\TestCase;

/**
 * Class PasswordRecoveryTest
 *
 * @package Tests\Service\Email\Messages
 */
class PasswordRecoveryTest extends TestCase
{
    /** @var PasswordRecovery */
    private $sut;

    public function setUp()
    {
        $this->sut = new PasswordRecovery();
        $this->sut->setUser($this->mock()->get('Api\Entities\User'));
        $this->sut->setSender('sender@email.com');
    }

    public function testGetMessage()
    {
        $password = 'password';
        $email = 'email@email.com';

        $this->mock()->get('Api\Entities\User')
             ->expects($this->once())
             ->method('getEmail')
             ->will($this->returnValue($email));

        $this->sut->setPassword($password);

        $response = $this->sut->getMessage();

        $this->assertInstanceOf('\Swift_Message', $response);
    }

    /**
     * @expectedException \Swift_RfcComplianceException
     */
    public function testGetMessageFailsIfUserEmailIsWrong()
    {
        $password = 'password';
        $this->sut->setPassword($password);
        $this->sut->getMessage();
    }

}
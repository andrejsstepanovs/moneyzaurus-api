<?php

namespace Tests\Service\Email\Messages;


use Api\Service\Email\Messages\ConnectionInvitation;
use Tests\TestCase;

/**
 * Class ConnectionInvitationTest
 *
 * @package Tests\Service\Email\Messages
 */
class ConnectionInvitationTest extends TestCase
{
    /** @var ConnectionInvitation */
    private $sut;

    public function setUp()
    {
        $this->sut = new ConnectionInvitation();
        $this->sut->setConnection($this->mock()->get('Api\Entities\Connection'));
        $this->sut->setSender('sender@email.com');
    }

    public function testGetMessage()
    {
        $email = 'email@email.com';

        $user = $this->mock()->get('Api\Entities\User');

        $this->mock()->get('Api\Entities\Connection')
             ->expects($this->once())
             ->method('getUser')
             ->will($this->returnValue($user));

        $this->mock()->get('Api\Entities\Connection')
             ->expects($this->once())
             ->method('getParent')
             ->will($this->returnValue($user));

        $user->expects($this->exactly(2))
             ->method('getEmail')
             ->will($this->returnValue($email));

        $response = $this->sut->getMessage();

        $this->assertInstanceOf('\Swift_Message', $response);
    }

}
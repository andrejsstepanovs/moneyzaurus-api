<?php

namespace AcceptanceTests\Authenticate;

use AcceptanceTests\TestCase;

/**
 * Class PasswordRecoveryTest
 *
 * @package AcceptanceTests\User
 */
class PasswordRecoveryTest extends TestCase
{
    /**
     * @return array
     */
    public function testRegister()
    {
        return parent::registerNewUser();
    }

    /**
     * @depends testRegister
     *
     * @param array $user
     */
    public function testPasswordRecovery($user)
    {
        $post = array(
            'username' => $user['email'],
        );

        $response = $this->post('/authenticate/password-recovery', $post);
        $data = (array) $response->json();

        $this->assertTrue($data['success'], print_r($data, true));
    }

    public function testPasswordRecoveryForUnknownUser()
    {
        $post = array(
            'username' => 'unknown-test-email@email.com',
        );

        $response = $this->post('/authenticate/password-recovery', $post);
        $data = (array) $response->json();

        $this->assertFalse($data['success'], print_r($data, true));
    }
}

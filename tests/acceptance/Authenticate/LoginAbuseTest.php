<?php

namespace AcceptanceTests\Authenticate;

use AcceptanceTests\TestCase;

/**
 * Class LoginAbuseTest
 *
 * @package AcceptanceTests\Authenticate
 */
class LoginAbuseTest extends TestCase
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
     *
     * @return string
     */
    public function testLogin($user)
    {
        return parent::login($user);
    }

    /**
     * @depends testRegister
     *
     * @param array $user
     */
    public function testLoginMultipleTimes(array $user)
    {
        for($i = 1; $i <= 2; $i++) {
            $start = microtime(true);
            $postData = array(
                'username' => $user['email'],
                'password' => 'wrong password'
            );

            $response = $this->post('/authenticate/login', $postData);
            $data = (array)$response->json();

            $this->assertFalse($data['success']);

            $time = microtime(true) - $start;

            if ($i <= 1) {
                $this->assertLessThanOrEqual(2, $time);
            } else {
                $this->assertGreaterThanOrEqual(2, $time);
            }
        }
    }

}
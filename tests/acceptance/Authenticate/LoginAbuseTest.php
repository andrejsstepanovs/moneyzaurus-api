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
            $start = time();
            $postData = array(
                'username' => $user['email'],
                'password' => 'wrong password'
            );

            $response = $this->post('/authenticate/login', $postData);
            $data = (array)$response->json();

            $this->assertFalse($data['success']);

            $time = time() - $start;

            if ($i > 1) {
                $this->assertGreaterThanOrEqual(2, $time);
            } else {
                $this->assertLessThan(2, $time);
            }
        }
    }

}
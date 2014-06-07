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
        $config            = $this->getConfig();
        $security          = $config['security'];
        $expectedSleepTime = $security['login_abuse_sleep_time'];
        $maxLoginAttempts  = $security['max_login_attempts'];

        for($i = 1; $i <= $maxLoginAttempts; $i++) {
            $start = time();
            $postData = array(
                'username' => $user['email'],
                'password' => 'wrong password'
            );

            $response = $this->post('/authenticate/login', $postData);
            $data = (array)$response->json();

            $this->assertFalse($data['success']);

            $time = time() - $start;

            if ($i >= $maxLoginAttempts) {
                $this->assertGreaterThanOrEqual($expectedSleepTime, $time);
            } else {
                $this->assertLessThanOrEqual($expectedSleepTime, $time, 'iterator=' . $i);
            }
        }
    }

}
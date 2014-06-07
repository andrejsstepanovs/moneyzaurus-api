<?php

namespace AcceptanceTests\Authenticate;

use AcceptanceTests\TestCase;

/**
 * Class TokenExpireTest
 *
 * @package AcceptanceTests\Authenticate
 */
class TokenExpireTest extends TestCase
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
    public function testLoginMultipleTimes(array $user)
    {
        $token = parent::login($user);
        $this->assertNotEmpty($token);

        $response = $this->get('/user/data?token=' . $token);
        $data = (array)$response->json();
        $this->assertTrue($data['success']);

        $seconds = $this->getTokenIntervalInSeconds();
        sleep($seconds + 1);

        try {
            $response = $this->get('/user/data?token=' . $token);
            $this->fail('Token should be expired by now.' . print_r($response, true));
        } catch (\RuntimeException $exc) {

            $config = $this->getConfig();
            $isDevMode = $config[\SlimApi\Kernel\Config::DEVMODE];

            if (!$isDevMode) {
                $errorLog = $this->getErrorLogFile();
                $this->assertFileExists($errorLog);
                file_put_contents($errorLog, '');
            }
        }
    }

}
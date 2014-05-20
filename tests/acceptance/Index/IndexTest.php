<?php

namespace Tests\Acceptance\Transactions;

use Tests\Acceptance\TestCase;

/**
 * Class IndexTest
 *
 * @package Tests\Acceptance\Transactions
 */
class IndexTest extends TestCase
{
    public function testIndexRespondsWithExpectedJson()
    {
        $response = $this->get('/');

        $this->assertContains('json', strval($response->getHeader('content-type')));

        $responseData = (array)$response->json();

        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('version', $responseData);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('process', $responseData);
        $this->assertEquals('V1', $responseData['version']);
    }

    public function testRegisterNewUserData()
    {
        $postData = array(
            'username' => 'email@email.com',
            'password' => 'abc123'
        );

        $response = $this->post('/user/register', $postData);

        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertEquals(1, $data['data']['id']);
        $this->assertEquals(1, $data['data']['state']);
        $this->assertEquals($postData['username'], $data['data']['email']);
        $this->assertEquals('user', $data['data']['role']);
    }
}
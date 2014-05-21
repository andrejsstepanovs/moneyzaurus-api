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
        $email    = 'email@email.com';
        $password = 'abc 123 ., *#$';

        $postData = array(
            'username' => $email,
            'password' => $password
        );

        $response = $this->post('/user/register', $postData);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertEquals(1, $data['data']['id']);
        $this->assertEquals(1, $data['data']['state']);
        $this->assertEquals($postData['username'], $data['data']['email']);
        $this->assertEquals('user', $data['data']['role']);

        // save for further tests
        define('USER_EMAIL', $email);
        define('USER_PASSWORD', $password);
        define('USER_ID', $data['data']['id']);
    }

    public function testFalseLogin()
    {
        $postData = array(
            'username' => USER_EMAIL,
            'password' => USER_PASSWORD . 'wrong'
        );

        $response = $this->post('/authenticate/login', $postData);
        $data = (array)$response->json();

        $this->assertFalse($data['success']);
    }

    public function testFalseLoginEmail()
    {
        $postData = array(
            'username' => 'unknown'.USER_EMAIL,
            'password' => USER_PASSWORD
        );

        $response = $this->post('/authenticate/login', $postData);
        $data = (array)$response->json();

        $this->assertFalse($data['success']);
    }

    public function testLoginAndLogout()
    {
        $postData = array(
            'username' => USER_EMAIL,
            'password' => USER_PASSWORD
        );

        $response = $this->post('/authenticate/login', $postData);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['data']['token']);
        $this->assertEquals(USER_ID, $data['data']['id']);


        $response = $this->get('/authenticate/logout?token=' . $data['data']['token']);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);


        $response = $this->post('/authenticate/login', $postData);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);

        define('ACCESS_TOKEN', $data['data']['token']);
    }

    public function testUserData()
    {
        $response = $this->get('/user/data?token=' . ACCESS_TOKEN);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['data']);
        $this->assertEquals(USER_ID, $data['data']['id']);
        $this->assertEquals(USER_EMAIL, $data['data']['email']);
    }

    public function testUserUpdate()
    {
        $name     = 'User Name';
        $locale   = 'lv_LV';
        $language = 'lv_LV';
        $timezone = 'Europe/Berlin';

        $post = array(
            'name'     => $name,
            'locale'   => $locale,
            'language' => $language,
            'timezone' => $timezone
        );

        $response = $this->post('/user/update?token=' . ACCESS_TOKEN, $post);

        $data = (array)$response->json();
        $this->assertTrue($data['success']);

        $response = $this->get('/user/data?token=' . ACCESS_TOKEN);
        $data = (array)$response->json();

        $this->assertEquals($name, $data['data']['name']);
        $this->assertEquals($language, $data['data']['language']);
        $this->assertEquals($locale, $data['data']['locale']);
        $this->assertEquals($timezone, $data['data']['timezone']);
    }

    public function testCreateNewTransaction()
    {
        $post = array(
            'item'     => 'Item Name',
            'group'    => 'Group Name',
            'price'    => '12',
            'currency' => 'EUR',
            'date'     => date('Y-m-d')
        );

        $response = $this->post('/transactions/add?token=' . ACCESS_TOKEN, $post);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertEquals(1, $data['data']['id']);
    }
}
<?php

namespace AcceptanceTests\User;

use AcceptanceTests\TestCase;

/**
 * Class RegisterAndLoginTest
 *
 * @package AcceptanceTests\User
 */
class RegisterAndLoginTest extends TestCase
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
    public function testLoginWithFalsePassword(array $user)
    {
        $postData = array(
            'username' => $user['email'],
            'password' => 'wrong password'
        );

        $response = $this->post('/authenticate/login', $postData);
        $data = (array)$response->json();

        $this->assertFalse($data['success']);
    }

    /**
     * @depends testRegister
     *
     * @param array $user
     */
    public function testLoginWithFalseEmail(array $user)
    {
        $postData = array(
            'username' => 'unknown@email.com',
            'password' => $user['password']
        );

        $response = $this->post('/authenticate/login', $postData);
        $data = (array)$response->json();

        $this->assertFalse($data['success']);
    }

    /**
     * @depends testLogin
     * @depends testRegister
     *
     * @param string $token
     * @param array  $user
     */
    public function testGetUserData($token, array $user)
    {
        $response = $this->get('/user/data?token=' . $token);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['data']);
        $this->assertEquals($user['id'], $data['data']['id']);
        $this->assertEquals($user['email'], $data['data']['email']);
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     */
    public function testUserUpdate($token)
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

        $response = $this->post('/user/update?token=' . $token, $post);

        $data = (array)$response->json();
        $this->assertTrue($data['success']);

        $response = $this->get('/user/data?token=' . $token);
        $data = (array)$response->json();

        $this->assertEquals($name, $data['data']['name']);
        $this->assertEquals($language, $data['data']['language']);
        $this->assertEquals($locale, $data['data']['locale']);
        $this->assertEquals($timezone, $data['data']['timezone']);
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     */
    public function testLogout($token)
    {
        $response = $this->get('/authenticate/logout?token=' . $token);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
    }
}
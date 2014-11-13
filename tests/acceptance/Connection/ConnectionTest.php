<?php

namespace AcceptanceTests\Connection;

use AcceptanceTests\TestCase;

/**
 * Class GroupsTest
 *
 * @package AcceptanceTests\Connection
 */
class ConnectionTest extends TestCase
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
     * @depends testLogin
     *
     * @param string $token
     */
    public function testAddNotExistingUser($token)
    {
        $post = array(
            'email' => 'unknown_email@email.com',
        );

        $response = $this->post('/connection/add?token=' . $token, $post);
        $data = (array) $response->json();

        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('message', $data);
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     */
    public function testAddWrongEmail($token)
    {
        $post = array(
            'email' => 'unknown_email',
        );

        $response = $this->post('/connection/add?token=' . $token, $post);
        $data = (array) $response->json();

        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('message', $data);
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     *
     * @return array
     */
    public function testAddFriend($token)
    {
        $friend = $this->registerNewUser();

        $post = array(
            'email' => $friend['email'],
        );

        $response = $this->post('/connection/add?token=' . $token, $post);
        $data = (array) $response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('id', $data['data']);
        $this->assertGreaterThan(0, $data['data']['id']);

        return $friend;
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     *
     * @return array
     */
    public function testListUser($token)
    {
        $response = $this->get('/connection/list?token=' . $token);
        $data = (array) $response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('count', $data);
        $this->assertGreaterThan(0, $data['count']);
        $this->assertArrayHasKey('data', $data);
        $this->assertGreaterThan(0, $data['data'][0]['id']);

        return $data['data'];
    }

    /**
     * @depends testLogin
     * @depends testListUser
     *
     * @param string $token
     * @param array  $listData
     */
    public function testAcceptYourOwnInvitationIsNotAllowed($token, array $listData)
    {
        foreach ($listData as $list) {
            $id = $list['id'];
            $response = $this->post('/connection/accept/ ' . $id . '?token=' . $token);
            $data = (array) $response->json();

            $this->assertFalse($data['success']);
            $this->assertNotEmpty($data['message']);
        }
    }

    /**
     * @depends testLogin
     * @depends testListUser
     *
     * @param string $token
     * @param array  $listData
     */
    public function testRejectYourOwnInvitationIsNotAllowed($token, array $listData)
    {
        foreach ($listData as $list) {
            $id = $list['id'];
            $response = $this->post('/connection/reject/' . $id . '?token=' . $token);
            $data = (array) $response->json();

            $this->assertFalse($data['success']);
            $this->assertNotEmpty($data['message']);
        }
    }

    /**
     * @depends testAddFriend
     *
     * @param array $friend
     *
     * @return array
     */
    public function testListParent(array $friend)
    {
        $token = $this->testLogin($friend);

        $response = $this->get('/connection/list?token=' . $token . '&parent=1');
        $data = (array) $response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('count', $data);
        $this->assertGreaterThan(0, $data['count']);
        $this->assertArrayHasKey('data', $data);

        return $data['data'];
    }

    /**
     * @depends testAddFriend
     * @depends testListParent
     *
     * @param array $friend
     * @param array $listData
     */
    public function testAccept(array $friend, array $listData)
    {
        $token = $this->testLogin($friend);

        foreach ($listData as $list) {
            $id = $list['id'];
            $response = $this->post('/connection/accept/' . $id . '?token=' . $token);
            $data = (array) $response->json();

            $this->assertTrue($data['success']);
        }
    }

    /**
     * @depends testAddFriend
     * @depends testListParent
     *
     * @param array $friend
     * @param array $listData
     */
    public function testAcceptAcceptedWillFail(array $friend, array $listData)
    {
        $token = $this->testLogin($friend);

        foreach ($listData as $list) {
            $id = $list['id'];
            $response = $this->post('/connection/accept/ ' . $id . '?token=' . $token);
            $data = (array) $response->json();

            $this->assertFalse($data['success']);
            $this->assertNotEmpty($data['message']);
        }
    }

    /**
     * @depends testAddFriend
     * @depends testListParent
     *
     * @param array $friend
     * @param array $listData
     */
    public function testReject(array $friend, array $listData)
    {
        $token = $this->testLogin($friend);

        foreach ($listData as $list) {
            $id = $list['id'];
            $response = $this->post('/connection/reject/' . $id . '?token=' . $token);
            $data = (array) $response->json();

            $this->assertTrue($data['success']);
        }
    }

    /**
     * @depends testAddFriend
     * @depends testListParent
     *
     * @param array $friend
     * @param array $listData
     */
    public function testRejectRejected(array $friend, array $listData)
    {
        $token = $this->testLogin($friend);

        foreach ($listData as $list) {
            $id = $list['id'];
            $response = $this->post('/connection/reject/' . $id . '?token=' . $token);
            $data = (array) $response->json();

            $this->assertFalse($data['success']);
            $this->assertNotEmpty($data['message']);
        }
    }
}

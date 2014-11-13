<?php

namespace AcceptanceTests\Transactions;

/**
 * Class FetchConnectedTest
 *
 * @package AcceptanceTests\Transactions
 */
class FetchConnectedTest extends CreateTest
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
     * @return array
     */
    public function correctDataProvider()
    {
        return array(
            array(
                array(
                    'item'     => 'apple',
                    'group'    => 'food',
                    'price'    => '1.00',
                    'currency' => 'EUR',
                    'date'     => '2000-01-01',
                ),
            ),
            array(
                array(
                    'item'     => 'banana',
                    'group'    => 'food',
                    'price'    => '0.5',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d'),
                ),
            ),
        );
    }

    /**
     * @dataProvider correctDataProvider
     *
     * @depends testLogin
     */
    public function testCreateTransactions(array $post, $token)
    {
        parent::testSuccessfulCreate($post, $token);
    }

    /**
     * @return string
     */
    public function testLoginFriend()
    {
        $friend = $this->registerNewUser();

        return $this->testLogin($friend);
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     */
    public function testGetUserEmail($token)
    {
        $response = $this->get('/user/data?token=' . $token);
        $data = (array) $response->json();
        $this->assertArrayHasKey('success', $data, print_r($data, true));
        $this->assertTrue($data['success']);

        return $data['data']['email'];
    }

    /**
     * @depends testLoginFriend
     * @depends testGetUserEmail
     *
     * @return string friend token
     */
    public function testAddConnection($friendToken, $parentEmail)
    {
        $response = $this->post('/connection/add?token=' . $friendToken, ['email' => $parentEmail]);
        $data = (array) $response->json();
        $this->assertArrayHasKey('success', $data, print_r($data, true));
        $this->assertTrue($data['success']);

        return $data['data']['id'];
    }

    /**
     * @depends testLoginFriend
     */
    public function testGetTransactionListWithNotAcceptedConnection($friendToken)
    {
        $response = $this->get('/transactions/list?token=' . $friendToken);
        $data = (array) $response->json();

        $this->assertArrayHasKey('success', $data, print_r($data, true));
        $this->assertTrue($data['success']);
        $this->assertEquals(0, $data['count']);
        $this->assertEmpty($data['data']);
    }

    /**
     * @depends testLogin
     *
     * @return array
     */
    public function testGetTransactionsList($token)
    {
        $response = $this->get('/transactions/list?token=' . $token);
        $data = (array) $response->json();

        $this->assertArrayHasKey('success', $data, print_r($data, true) . ' token=' . $token);
        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['data']);

        return $data['data'];
    }

    /**
     * @depends testGetTransactionsList
     * @depends testLoginFriend
     */
    public function testGetTransactionDataWithNotAcceptedConnection(array $transactionList, $friendToken)
    {
        foreach ($transactionList as $transaction) {
            $response = $this->get('/transactions/id/' . $transaction['id'] . '?token=' . $friendToken);
            $data = (array) $response->json();
            $this->assertArrayHasKey('success', $data, print_r($data, true));
            $this->assertFalse($data['success']);
        }
    }

    /**
     * @depends testGetTransactionsList
     * @depends testLoginFriend
     */
    public function testUpdateListWithNotAcceptedConnection(array $transactionList, $friendToken)
    {
        foreach ($transactionList as $transaction) {
            $post = ['item' => 'TEST'];
            $response = $this->post('/transactions/update/' . $transaction['id'] . '?token=' . $friendToken, $post);
            $data = (array) $response->json();
            $this->assertArrayHasKey('success', $data, print_r($data, true));
            $this->assertFalse($data['success']);
        }
    }

    /**
     * @depends testGetTransactionsList
     * @depends testLoginFriend
     */
    public function testDeleteTransactionWithNotAcceptedConnection(array $transactionList, $friendToken)
    {
        foreach ($transactionList as $transaction) {
            $response = $this->delete('/transactions/remove/' . $transaction['id'] . '?token=' . $friendToken);
            $data = (array) $response->json();
            $this->assertArrayHasKey('success', $data, print_r($data, true));
            $this->assertFalse($data['success']);
        }
    }

    /**
     * @depends testAddConnection
     * @depends testLogin
     */
    public function testAcceptConnection($connectionId, $token)
    {
        $response = $this->post('/connection/accept/' . (int) $connectionId . '?token=' . $token);
        $data = (array) $response->json();
        $this->assertArrayHasKey('success', $data, print_r($data, true));
        $this->assertTrue($data['success']);
    }

    /**
     * @depends testLoginFriend
     */
    public function testGetTransactionListWithAcceptedConnection($friendToken)
    {
        $response = $this->get('/transactions/list?token=' . $friendToken);
        $data = (array) $response->json();

        $this->assertArrayHasKey('success', $data, print_r($data, true));
        $this->assertTrue($data['success']);
        $this->assertGreaterThan(0, $data['count']);
        $this->assertNotEmpty($data['data']);
    }

    /**
     * @depends testGetTransactionsList
     * @depends testLoginFriend
     */
    public function testGetTransactionDataWithAcceptedConnection(array $transactionList, $friendToken)
    {
        foreach ($transactionList as $transaction) {
            $response = $this->get('/transactions/id/' . $transaction['id'] . '?token=' . $friendToken);
            $data = (array) $response->json();
            $this->assertArrayHasKey('success', $data, print_r($data, true));
            $this->assertTrue($data['success']);
        }
    }

    /**
     * @depends testGetTransactionsList
     * @depends testLoginFriend
     */
    public function testUpdateListWithAcceptedConnection(array $transactionList, $friendToken)
    {
        foreach ($transactionList as $transaction) {
            $post = ['item' => 'TEST'];
            $response = $this->post('/transactions/update/' . $transaction['id'] . '?token=' . $friendToken, $post);
            $data = (array) $response->json();
            $this->assertArrayHasKey('success', $data, print_r($data, true));
            $this->assertTrue($data['success']);
        }
    }

    /**
     * @depends testGetTransactionsList
     * @depends testLoginFriend
     */
    public function testDeleteTransactionWithAcceptedConnection(array $transactionList, $friendToken)
    {
        foreach ($transactionList as $transaction) {
            $response = $this->delete('/transactions/remove/' . $transaction['id'] . '?token=' . $friendToken);
            $data = (array) $response->json();
            $this->assertArrayHasKey('success', $data, print_r($data, true));
            $this->assertTrue($data['success']);
        }
    }
}

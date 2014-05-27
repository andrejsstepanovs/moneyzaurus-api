<?php

namespace Tests\Acceptance\Transactions;

/**
 * Class FetchTest
 *
 * @package Tests\Acceptance\Transactions
 */
class FetchTest extends CreateTest
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
                    'date'     => '2000-01-01'
                ),
            ),
            array(
                array(
                    'item'     => 'banana',
                    'group'    => 'food',
                    'price'    => '0.5',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
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
     * @depends testLogin
     *
     * @param string $token
     */
    public function testGetTransactionList($token)
    {
        $params = array(
            'offset' => 0,
            'limit'  => 100
        );
        $response = $this->get('/transactions/list?token=' . $token, $params);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['data']);

        $dataProviderData = $this->correctDataProvider();
        $dataProviderData = array_reverse($dataProviderData);
        foreach ($dataProviderData as $key => $transaction) {
            $transaction         = $transaction[0];
            $responseTransaction = $data['data'][$key];

            $this->assertGreaterThan(0, $responseTransaction['id']);
            $this->assertEquals($transaction['item'], $responseTransaction['itemName']);
            $this->assertEquals($transaction['group'], $responseTransaction['groupName']);
            $this->assertEquals($transaction['price'], $responseTransaction['price']);
            $this->assertEquals($transaction['date'], $responseTransaction['date']);
        }

        return $data['data'];
    }

    /**
     * @depends testLogin
     * @depends testGetTransactionList
     *
     * @param string $token
     * @param array  $transactionList
     */
    public function testTransactionId($token, array $transactionList)
    {
        foreach ($transactionList as $transaction) {
            $response = $this->get('/transactions/id/' . $transaction['id'] . '?token=' . $token);
            $data = (array)$response->json();

            $this->assertTrue($data['success']);
            $this->assertNotEmpty($data['data']);
            $this->assertGreaterThan(0, $data['data']['id']);
        }
    }

    /**
     * @depends testLogin
     * @depends testGetTransactionList
     *
     * @param string $token
     * @param array  $transactionList
     */
    public function testTransactionIdForUnknownId($token, array $transactionList)
    {
        $ids = array_column($transactionList, 'id');
        $maxId = max($ids);

        $response = $this->get('/transactions/id/' . ($maxId + 1) . '?token=' . $token);
        $data = (array)$response->json();

        $this->assertFalse($data['success']);
    }

    /**
     * @depends testLogin
     * @depends testGetTransactionList
     *
     * @param string $token
     * @param array  $transactionList
     */
    public function testTransactionIdUnknownUserId($token, array $transactionList)
    {
        $ids = array_column($transactionList, 'id');
        $minId = min($ids);

        $response = $this->get('/transactions/id/' . ($minId - 1) . '?token=' . $token);
        $data = (array)$response->json();

        $this->assertFalse($data['success']);
    }
}
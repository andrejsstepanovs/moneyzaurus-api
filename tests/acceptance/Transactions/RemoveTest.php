<?php

namespace AcceptanceTests\Transactions;

/**
 * Class RemoveTest
 *
 * @package AcceptanceTests\Transactions
 */
class RemoveTest extends CreateTest
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
        $response = $this->get('/transactions/list?token=' . $token);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['data']);

        return $data['data'];
    }

    /**
     * @depends testLogin
     * @depends testGetTransactionList
     *
     * @param string $token
     * @param array  $transactionList
     */
    public function testRemove($token, array $transactionList)
    {
        foreach ($transactionList as $transaction) {
            $response = $this->delete('/transactions/remove/' . $transaction['id'] . '?token=' . $token);
            $data = (array)$response->json();

            $this->assertTrue($data['success']);
        }
    }

    /**
     * @depends testLogin
     * @depends testGetTransactionList
     *
     * @param string $token
     * @param array  $transactionList
     */
    public function testRemoveUnknown($token, array $transactionList)
    {
        $ids = array_column($transactionList, 'id');
        $unknownId = max($ids) + 1;

        $response = $this->delete('/transactions/remove/' . $unknownId . '?token=' . $token);
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
    public function testRemoveOtherUserTransaction($token, array $transactionList)
    {
        $ids = array_column($transactionList, 'id');
        $unknownId = max($ids) - 1;

        if ($unknownId > 0) {
            $response = $this->delete('/transactions/remove/' . $unknownId . '?token=' . $token);
            $data = (array)$response->json();

            $this->assertFalse($data['success']);
        }
    }
}
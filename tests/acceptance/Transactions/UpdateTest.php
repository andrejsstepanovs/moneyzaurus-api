<?php

namespace Tests\Acceptance\Transactions;

/**
 * Class UpdateTest
 *
 * @package Tests\Acceptance\Transactions
 */
class UpdateTest extends CreateTest
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
    public function testUpdate($token, array $transactionList)
    {
        $updatedData = array();
        foreach ($transactionList as $transaction) {
            $post = array(
                'item'     => 'TEST ITEM',
                'group'    => 'TEST GROUP',
                'price'    => '100.99',
                'currency' => 'EUR',
                'date'     => '2000-01-01',
            );

            $response = $this->post('/transactions/update/' . $transaction['id'] . '?token=' . $token, $post);
            $data = (array)$response->json();

            $this->assertTrue($data['success']);

            $this->assertNotEmpty($data['data']);
            $this->assertEquals($transaction['id'], $data['data']['id']);

            $updatedData[] = $post;
        }

        return $updatedData;
    }

    /**
     * @depends testLogin
     * @depends testGetTransactionList
     * @depends testUpdate
     */
    public function testUpdatedData($token, array $transactionList, array $updatedData)
    {
        foreach ($transactionList as $key => $transaction) {
            $response = $this->get('/transactions/id/' . $transaction['id'] . '?token=' . $token);
            $data = (array)$response->json();

            $transactionData = $data['data'];
            $newData = $updatedData[$key];
            $this->assertEquals($newData['item'], $transactionData['itemName']);
            $this->assertEquals($newData['group'], $transactionData['groupName']);
            $this->assertEquals($newData['price'], $transactionData['price']);
            $this->assertEquals($newData['currency'], $transactionData['currency']);
            $this->assertEquals($newData['date'], $transactionData['date']);
        }
    }

}
<?php

namespace AcceptanceTests\Transactions;


/**
 * Class FailCreateTest
 *
 * @package AcceptanceTests\Transactions
 */
class FailCreateTest extends CreateTest
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
    public function wrongDataProvider()
    {
        return array(
            array(
                array(
                    'item'     => 'Item Name',
                    'group'    => 'Group Name',
                    'price'    => '12',
                    'currency' => 'UNKNOWN',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'apple',
                    'group'    => 'food',
                    'price'    => '12.34',
                    'currency' => 'EUR',
                    'date'     => 'WRONG'
                ),
            ),
            array(
                array(
                    'item'     => 'banana',
                    'group'    => 'food',
                    'price'    => 'WRONG PRICE',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => '',
                    'group'    => 'Group',
                    'price'    => '999',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'Item',
                    'group'    => '',
                    'price'    => '123',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'Item',
                    'group'    => 'Group',
                    'price'    => '',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
        );
    }

    /**
     * @dataProvider wrongDataProvider
     * @depends      testLogin
     *
     * @param string $token
     */
    public function testFailCreate(array $post, $token)
    {
        $response = $this->post('/transactions/add?token=' . $token, $post);
        $data = (array)$response->json();

        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('message', $data);
        $this->assertNotEmpty($data['message']);
    }
}
<?php

namespace Tests\Acceptance\Transactions;

use Tests\Acceptance\TestCase;

/**
 * Class IndexTest
 *
 * @package Tests\Acceptance\Transactions
 */
class CreateTest extends TestCase
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
                    'item'     => 'Item Name',
                    'group'    => 'Group Name',
                    'price'    => '12',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'Āķšādļļķī !:SL',
                    'group'    => 'ŠĶĻĀnņūīkls',
                    'price'    => '11.10',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'Test1',
                    'group'    => 'Test1',
                    'price'    => '10.95',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'Test2',
                    'group'    => 'Test1',
                    'price'    => '5.05',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'Test3',
                    'group'    => 'Test1',
                    'price'    => '5.05',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
        );
    }

    /**
     * @dataProvider correctDataProvider
     * @depends      testLogin
     *
     * @param string $token
     */
    public function testSuccessfulCreate(array $post, $token)
    {
        $response = $this->post('/transactions/add?token=' . $token, $post);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertGreaterThan(0, $data['data']['id']);
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
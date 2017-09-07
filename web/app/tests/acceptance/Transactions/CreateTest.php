<?php

namespace AcceptanceTests\Transactions;

use AcceptanceTests\TestCase;

/**
 * Class IndexTest
 *
 * @package AcceptanceTests\Transactions
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
                    'date'     => date('Y-m-d'),
                ),
            ),
            array(
                array(
                    'item'     => 'Āķšādļļķī !:SL',
                    'group'    => 'ŠĶĻĀnņūīkls',
                    'price'    => '11.10',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d'),
                ),
            ),
            array(
                array(
                    'item'     => 'Test1',
                    'group'    => 'Test1',
                    'price'    => '10.95',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d'),
                ),
            ),
            array(
                array(
                    'item'     => 'Test2',
                    'group'    => 'Test1',
                    'price'    => '5.05',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d'),
                ),
            ),
            array(
                array(
                    'item'     => 'Test3',
                    'group'    => 'Test1',
                    'price'    => '5.05',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d'),
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
        $data = (array) $response->json();

        $this->assertTrue($data['success']);
        $this->assertGreaterThan(0, $data['data']['id']);
    }
}

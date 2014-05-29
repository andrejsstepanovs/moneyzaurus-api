<?php

namespace AcceptanceTests\Predict;

use AcceptanceTests\Transactions\CreateTest;

/**
 * Class GroupTest
 *
 * @package AcceptanceTests\Distinct
 */
class GroupTest extends CreateTest
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
                    'item'     => 'banana',
                    'group'    => 'food',
                    'price'    => '0.5',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
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
            array(
                array(
                    'item'     => 'banana',
                    'group'    => 'food',
                    'price'    => '0.5',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'banana',
                    'group'    => 'fruit',
                    'price'    => '0.5',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'banana',
                    'group'    => 'fruit',
                    'price'    => '0.5',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'banana',
                    'group'    => 'fruits',
                    'price'    => '0.5',
                    'currency' => 'EUR',
                    'date'     => date('Y-m-d')
                ),
            ),
            array(
                array(
                    'item'     => 'TV',
                    'group'    => 'home',
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
    public function testPredict($token)
    {
        $post = array(
            'item' => 'banana'
        );
        $response = $this->post('/predict/group?token=' . $token, $post);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('count', $data);
        $this->assertNotEmpty($data['data']);

        $this->assertEquals(2, $data['count']);
        $this->assertEquals(
            array('food', 'fruit'),
            $data['data']
        );
    }

}
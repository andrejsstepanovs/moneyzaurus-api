<?php

namespace AcceptanceTests\Chart;

use AcceptanceTests\Transactions\CreateTest;

/**
 * Class PieTest
 *
 * @package AcceptanceTests\Chart
 */
class PieTest extends CreateTest
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
                    'price'    => '30.00',
                    'currency' => 'EUR',
                    'date'     => '2000-01-01'
                ),
            ),
            array(
                array(
                    'item'     => 'pear',
                    'group'    => 'food',
                    'price'    => '10.00',
                    'currency' => 'EUR',
                    'date'     => '2000-02-01'
                ),
            ),
            array(
                array(
                    'item'     => 'orange',
                    'group'    => 'fruit',
                    'price'    => '40.00',
                    'currency' => 'EUR',
                    'date'     => '2000-02-01'
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
    public function testSelectAll($token)
    {
        $params = [
            'token'    => $token,
            'currency' => 'EUR'
        ];

        $response = $this->get('/chart/pie?' . http_build_query($params));
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('currency', $data);
        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('till', $data);
        $this->assertNotEmpty($data['data']);

        $this->assertEquals(2, $data['count']);
        $this->assertEquals(
             [
                 [
                    'amount'    => '4000',
                    'groupId'   => 13,
                    'groupName' => 'food',
                    'percent'   => 50,
                    'price'     => '40.00',
                    'money'     => '€40.00'
                 ],
                 [
                     'amount'    => 4000,
                     'groupId'   => 14,
                     'groupName' => 'fruit',
                     'percent'   => 50,
                     'price'     => 40.00,
                     'money'     => '€40.00'
                 ]
             ],
             $data['data']
        );
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     */
    public function testSelectDate($token)
    {
        $params = [
            'token'    => $token,
            'currency' => 'EUR',
            'from'     => '2000-02-01',
            'till'     => '2000-02-01',
        ];

        $response = $this->get('/chart/pie?' . http_build_query($params));
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('currency', $data);
        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('till', $data);
        $this->assertNotEmpty($data['data']);
        $this->assertEquals(2, $data['count']);

        $this->assertEquals(
             [
                 [
                     'amount'    => 4000,
                     'groupId'   => 14,
                     'groupName' => 'fruit',
                     'percent'   => 80,
                     'price'     => 40.00,
                     'money'     => '€40.00'
                 ],
                 [
                     'amount'    => 1000,
                     'groupId'   => 13,
                     'groupName' => 'food',
                     'percent'   => 20,
                     'price'     => 10.00,
                     'money'     => '€10.00'
                 ]
             ],
             $data['data']
        );
    }

    /**
     * @depends testLogin
     *
     * @param string $token
     */
    public function testSelectDateReturnsOne($token)
    {
        $params = [
            'token'    => $token,
            'currency' => 'EUR',
            'from'     => '2000-01-01',
            'till'     => '2000-01-30',
        ];

        $response = $this->get('/chart/pie?' . http_build_query($params));
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('currency', $data);
        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('till', $data);
        $this->assertNotEmpty($data['data']);
        $this->assertEquals(1, $data['count']);

        $this->assertEquals(
             [
                 [
                     'amount'    => 3000,
                     'groupId'   => 13,
                     'groupName' => 'food',
                     'percent'   => 100,
                     'price'     => 30.00,
                     'money'     => '€30.00'
                 ]
             ],
             $data['data']
        );
    }
}
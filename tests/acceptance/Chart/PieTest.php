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
                    'price'    => '1.00',
                    'currency' => 'EUR',
                    'date'     => '2000-01-01'
                ),
            ),
            array(
                array(
                    'item'     => 'pear',
                    'group'    => 'food',
                    'price'    => '3.5',
                    'currency' => 'EUR',
                    'date'     => '2000-02-01'
                ),
            ),
            array(
                array(
                    'item'     => 'orange',
                    'group'    => 'fruit',
                    'price'    => '1',
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
                     'amount'    => 450,
                     'groupId'   => 1,
                     'groupName' => 'food',
                     'percent'   => 81.818181818182,
                     'price'     => 4.50,
                     'money'     => '€4.50'
                 ],
                 [
                     'amount'    => 100,
                     'groupId'   => 2,
                     'groupName' => 'fruit',
                     'percent'   => 18.181818181818,
                     'price'     => 1.00,
                     'money'     => '€1.00'
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
                     'amount'    => 350,
                     'groupId'   => 1,
                     'groupName' => 'food',
                     'percent'   => 77.777777777778,
                     'price'     => 3.50,
                     'money'     => '€3.50'
                 ],
                 [
                     'amount'    => 100,
                     'groupId'   => 2,
                     'groupName' => 'fruit',
                     'percent'   => 22.222222222222,
                     'price'     => 1.00,
                     'money'     => '€1.00'
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
                     'amount'    => 100,
                     'groupId'   => 1,
                     'groupName' => 'food',
                     'percent'   => 100,
                     'price'     => 1.00,
                     'money'     => '€1.00'
                 ]
             ],
             $data['data']
        );
    }
}
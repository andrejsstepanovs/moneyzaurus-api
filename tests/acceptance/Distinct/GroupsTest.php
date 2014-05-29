<?php

namespace AcceptanceTests\Distinct;

use AcceptanceTests\Transactions\CreateTest;

/**
 * Class GroupsTest
 *
 * @package AcceptanceTests\Distinct
 */
class GroupsTest extends CreateTest
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
    public function testGroups($token)
    {
        $response = $this->get('/distinct/groups?token=' . $token);
        $data = (array)$response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('count', $data);
        $this->assertNotEmpty($data['data']);

        $this->assertEquals(2, $data['count']);
        $this->assertEquals(
            array('food', 'home'),
            $data['data']
        );
    }

}
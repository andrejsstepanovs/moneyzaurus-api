<?php

namespace Tests\Controller\Login;

use Api\Controller\Predict\GroupController;
use Tests\TestCase;

/**
 * Class GroupControllerTest
 *
 * @package Tests
 */
class GroupControllerTest extends TestCase
{
    /** @var GroupController */
    private $sut;

    public function setUp()
    {
        $this->sut = new GroupController();
        $this->sut->setPredictGroup($this->mock()->get('Api\Service\Predict\Group'));
    }

    public function dataProvider()
    {
        return array(
            array(
                array(),
                array(
                    'success' => true,
                    'count'   => 0,
                    'data'    => array(),
                )
            ),
            array(
                array(
                    array('name' => 'test')
                ),
                array(
                    'success' => true,
                    'count'   => 1,
                    'data'    => array('test'),
                )
            ),
            array(
                array(
                    array('name' => 'apple'),
                    array('name' => 'banana')
                ),
                array(
                    'success' => true,
                    'count'   => 2,
                    'data'    => array('apple', 'banana'),
                )
            ),
        );
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $groups
     * @param array $expected
     */
    public function testWillReturnExpected(array $groups, array $expected)
    {
        $connectedUserIds = array();
        $item = 'item';

        $this->mock()->get('Api\Service\Predict\Group')
             ->expects($this->once())
             ->method('predict')
             ->will($this->returnValue($groups));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $item);

        $this->assertEquals($expected, $response);
    }

}
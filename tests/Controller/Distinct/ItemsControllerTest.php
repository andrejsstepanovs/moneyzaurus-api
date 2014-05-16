<?php

namespace Tests\Controller\Distinct;

use Api\Controller\Distinct\ItemsController;
use Tests\TestCase;
use Api\Entities\Item;

/**
 * Class ItemsControllerTest
 *
 * @package Tests
 */
class ItemsControllerTest extends TestCase
{
    /** @var ItemsController */
    private $sut;

    public function setUp()
    {
        $this->sut = new ItemsController();
        $this->sut->setItemRepository($this->mock()->get('Doctrine\ORM\EntityRepository'));
    }

    /**
     * @return Item|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getItemMock($name)
    {
        $itemMock = $this
            ->getMockBuilder('Api\Entities\Item')
            ->setMethods(array('getName'))
            ->getMock();

        $itemMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $itemMock;
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array(
                array('Apple'),
                array(
                    'success' => true,
                    'count'   => 1,
                    'data' => array('Apple')
                )
            ),
            array(
                array(),
                array(
                    'success' => true,
                    'count'   => 0,
                    'data' => array()
                )
            ),
            array(
                array(''),
                array(
                    'success' => true,
                    'count'   => 1,
                    'data' => array('')
                )
            ),
            array(
                array('apple', 'banana', 'orange'),
                array(
                    'success' => true,
                    'count'   => 3,
                    'data' => array('apple', 'banana', 'orange')
                )
            ),
            array(
                array('apple', 'banana', 'apple', 'banana'),
                array(
                    'success' => true,
                    'count'   => 2,
                    'data' => array('apple', 'banana')
                )
            ),
        );
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $names
     * @param array $expected
     */
    public function testResponseWillReturnExpected(array $names, array $expected)
    {
        $userId = 123;
        $connectedUserIds = array(456);

        $this->mock()->get('Api\Entities\User')
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($userId));

        $results = array();
        foreach ($names as $name) {
            $results[] = $this->getItemMock($name);
        }

        $this->mock()->get('Doctrine\ORM\EntityRepository')
            ->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('user' => array(123, 456))))
            ->will($this->returnValue($results));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds);

        $this->assertTrue(is_array($response));
        $this->assertEquals($expected, $response);
    }

}
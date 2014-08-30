<?php

namespace Tests\Controller\Distinct;

use Api\Controller\Distinct\ItemsController;
use Tests\TestCase;

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
        $this->sut->setItemsData($this->mock()->get('Api\Service\Items\Data'));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array(
                array('Apple'),
                null,
                array(
                    'success' => true,
                    'count'   => 1,
                    'data' => array('Apple')
                )
            ),
            array(
                array(),
                '2014-12-01',
                array(
                    'success' => true,
                    'count'   => 0,
                    'data' => array()
                )
            ),
            array(
                array(''),
                '2014-01-01',
                array(
                    'success' => true,
                    'count'   => 1,
                    'data' => array('')
                )
            ),
            array(
                array('apple', 'banana', 'orange'),
                null,
                array(
                    'success' => true,
                    'count'   => 3,
                    'data' => array('apple', 'banana', 'orange')
                )
            ),
            array(
                array('apple', 'banana'),
                null,
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
     * @param array     $names
     * @param \DateTime $dateFrom
     * @param array     $expected
     */
    public function testResponseWillReturnExpected(array $names, $dateFrom, array $expected)
    {
        $count    = 100;
        $userId   = 123;
        $timeZone = 'Europe/Berlin';
        $connectedUserIds = array(456);

        $this->mock()->get('Api\Entities\User')
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($userId));

        $this->mock()->get('Api\Entities\User')
            ->expects($this->any())
            ->method('getTimezone')
            ->will($this->returnValue($timeZone));

        $results = array();
        foreach ($names as $name) {
            $results[] = $name;
        }

        $dateFromObj = null;
        if ($dateFrom) {
            $dateFromObj = new \DateTime($dateFrom, new \DateTimeZone($timeZone));
        }

        $this->mock()->get('Api\Service\Items\Data')
            ->expects($this->once())
            ->method('getItems')
            ->with(
                $this->equalTo(array(123, 456)),
                $this->equalTo($dateFromObj)
            )
            ->will($this->returnValue($results));

        $response = $this->sut->getResponse(
            $this->mock()->get('Api\Entities\User'),
            $connectedUserIds,
            $dateFrom,
            $count
        );

        $this->assertTrue(is_array($response));
        $this->assertEquals($expected, $response);
    }

}
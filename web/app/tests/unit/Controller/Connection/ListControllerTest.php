<?php

namespace Tests\Controller\Connection;

use Api\Controller\Connection\ListController;
use Tests\TestCase;

/**
 * Class ListControllerTest
 *
 * @package Tests
 */
class ListControllerTest extends TestCase
{
    /** @var ListController */
    private $sut;

    public function setUp()
    {
        $this->sut = new ListController();
        $this->sut->setConnectionData($this->mock()->get('Api\Service\Connection\Data'));
    }

    public function testNoConnectedUsersFoundWillReturnFalse()
    {
        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('findByUser')
             ->will($this->returnValue(array()));

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('normalizeResults')
             ->will($this->returnValue(array()));

        $parent   = false;
        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $parent);

        $this->assertTrue($response['success']);
        $this->assertEquals(0, $response['count']);
        $this->assertEquals(array(), $response['data']);
    }

    public function testConnectedUsersFoundWillReturnExpected()
    {
        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('findByUser')
             ->will(
                 $this->returnValue(
                    array($this->mock()->get('Api\Entities\Connection'))
                 )
             );

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('normalizeResults')
             ->will($this->returnValue(array(array('apple' => 'banana'))));

        $parent   = false;
        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $parent);

        $this->assertTrue($response['success']);
        $this->assertEquals(1, $response['count']);
        $this->assertEquals(
            array(
                array(
                    'apple' => 'banana',
                ),
            ),
            $response['data']
        );
    }

    public function testNoConnectedParentsFoundWillReturnFalse()
    {
        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('findByParent')
             ->will($this->returnValue(array()));

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('normalizeResults')
             ->will($this->returnValue(array()));

        $parent   = true;
        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $parent);

        $this->assertTrue($response['success']);
        $this->assertEquals(0, $response['count']);
        $this->assertEquals(array(), $response['data']);
    }

    public function testConnectedParentFoundWillReturnExpected()
    {
        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('findByParent')
             ->will(
                 $this->returnValue(
                    array($this->mock()->get('Api\Entities\Connection'))
                 )
             );

        $this->mock()->get('Api\Service\Connection\Data')
             ->expects($this->once())
             ->method('normalizeResults')
             ->will($this->returnValue(array(array('apple' => 'banana'))));

        $parent   = true;
        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $parent);

        $this->assertTrue($response['success']);
        $this->assertEquals(1, $response['count']);
        $this->assertEquals(
            array(
                array(
                    'apple' => 'banana',
                ),
            ),
            $response['data']
        );
    }
}

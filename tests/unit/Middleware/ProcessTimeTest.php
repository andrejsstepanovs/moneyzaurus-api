<?php

namespace Tests\Middleware;

use Api\Middleware\ProcessTime;
use Tests\TestCase;

/**
 * Class ProcessTimeTest
 *
 * @package Tests
 */
class ProcessTimeTest extends TestCase
{
    /** @var ProcessTime */
    private $sut;

    public function setUp()
    {
        $this->sut = new ProcessTime();
        $this->sut->setApplication($this->mock()->get('\Slim\Slim'));
        $this->sut->setNextMiddleware($this->mock()->get('\Slim\Middleware'));
        $this->sut->setTime($this->mock()->get('\Api\Service\Time'));
    }

    public function testProcessTime()
    {
        $data = array('apple' => 'red');
        $expected = array(
            'apple'     => 'red',
            'timestamp' => '1388534400',
            'process'   => 'TimeDifference'
        );

        $this->mock()->get('\Slim\Slim')
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $this->mock()->get('\Api\Service\Time')
            ->expects($this->any())
            ->method('setTimezone')
            ->will($this->returnSelf());

        $this->mock()->get('\Api\Service\Time')
            ->expects($this->once())
            ->method('getMicroTimeDifference')
            ->will($this->returnValue('TimeDifference'));

        $this->mock()->get('\Api\Service\Time')
            ->expects($this->once())
            ->method('getDateTime')
            ->will($this->returnValue(new \DateTime('2014-01-01 00:00:00')));

        $this->mock()->get('\Slim\Slim')
            ->expects($this->once())
            ->method('setData')
            ->with($this->equalTo($expected));

        $this->mock()->get('\Slim\Slim')
            ->expects($this->once())
            ->method('config')
            ->with($this->equalTo('user'))
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Entities\User')
            ->expects($this->once())
            ->method('getTimezone')
            ->will($this->returnValue('Europe/Berlin'));

        $this->sut->call();
    }

    public function testAppDataIsNotArray()
    {
        $data = null;
        $expected = array(
            'timestamp' => '1388534400',
            'process'   => 'TimeDifference'
        );

        $this->mock()->get('\Slim\Slim')
             ->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->any())
             ->method('setTimezone')
             ->will($this->returnSelf());

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->once())
             ->method('getMicroTimeDifference')
             ->will($this->returnValue('TimeDifference'));

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->once())
             ->method('getDateTime')
             ->will($this->returnValue(new \DateTime('2014-01-01 00:00:00')));

        $this->mock()->get('\Slim\Slim')
             ->expects($this->once())
             ->method('setData')
             ->with($this->equalTo($expected));

        $this->mock()->get('\Slim\Slim')
             ->expects($this->once())
             ->method('config')
             ->with($this->equalTo('user'))
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Entities\User')
             ->expects($this->once())
             ->method('getTimezone')
             ->will($this->returnValue('Europe/Berlin'));

        $this->sut->call();
    }

    public function testNoUserFound()
    {
        $data = array('apple' => 'red');
        $expected = array(
            'apple'     => 'red',
            'timestamp' => '1388534400',
            'process'   => 'TimeDifference'
        );

        $this->mock()->get('\Slim\Slim')
             ->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->any())
             ->method('setTimezone')
             ->will($this->returnSelf());

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->once())
             ->method('getMicroTimeDifference')
             ->will($this->returnValue('TimeDifference'));

        $this->mock()->get('\Api\Service\Time')
             ->expects($this->once())
             ->method('getDateTime')
             ->will($this->returnValue(new \DateTime('2014-01-01 00:00:00')));

        $this->mock()->get('\Slim\Slim')
             ->expects($this->once())
             ->method('setData')
             ->with($this->equalTo($expected));

        $this->mock()->get('\Slim\Slim')
             ->expects($this->once())
             ->method('config')
             ->with($this->equalTo('user'))
             ->will($this->returnValue(null));

        $this->sut->call();
    }
}
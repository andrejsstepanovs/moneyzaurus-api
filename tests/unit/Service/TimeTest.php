<?php

namespace Tests\Service;

use Api\Service\Time;
use Tests\TestCase;

/**
 * Class TimeTest
 *
 * @package Tests\Service
 */
class TimeTest extends TestCase
{
    /** @var Time */
    private $sut;

    public function setUp()
    {
        $this->sut = new Time();
    }

    /**
     * @return array
     */
    public function getTimeDifferenceDataProvider()
    {
        return array(
            array(10, 10, 0),
            array(9.99, 10, 0.01),
        );
    }

    /**
     * @dataProvider getTimeDifferenceDataProvider
     *
     * @param float $start
     * @param float $stop
     * @param float $expected
     */
    public function testGetTimeDifference($start, $stop, $expected)
    {
        $result = $this->sut->getMicroTimeDifference($start, $stop);
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTimeDifferenceWithInvalidArguments()
    {
        $this->sut->getMicroTimeDifference(10, 9);
    }

    public function testSetTimezone()
    {
        $timezone = new \DateTimeZone('Europe/Berlin');
        $response = $this->sut->setTimezone($timezone);

        $responseTimezone = $this->sut->getTimezone();

        $this->assertInstanceOf(get_class($this->sut), $response);
        $this->assertInstanceOf(get_class($timezone), $responseTimezone);

        $this->assertEquals($responseTimezone->getLocation(), $timezone->getLocation());
    }

    public function testGetDateTimeWithTimezoneParameter()
    {
        $timezoneValue = 'Europe/Berlin';
        $timezone = new \DateTimeZone($timezoneValue);
        $time = '2014-01-01';

        $response = $this->sut->getDateTime($time, $timezone);

        $this->assertInstanceOf('DateTime', $response);
        $this->assertEquals($timezoneValue, $response->getTimezone()->getName());
        $this->assertEquals($time, $response->format('Y-m-d'));
    }
}
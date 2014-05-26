<?php

namespace Tests\Service\Transaction;

use Api\Service\Transaction\Date;
use Tests\TestCase;

/**
 * Class DateTest
 *
 * @package Tests
 */
class DateTest extends TestCase
{
    /** @var Date */
    private $sut;

    public function setUp()
    {
        $this->sut = new Date();
    }

    public function testUserTimezoneIsUsed()
    {
        $timezone     = 'Europe/Riga';
        $date         = '2014-12-30';
        $timeZoneRiga = new \DateTimeZone('Europe/Berlin');


        $this->mock()->get('Api\Entities\User')
            ->expects($this->once())
            ->method('getTimezone')
            ->will($this->returnValue($timezone));

        $result = $this->sut->getDateTime($this->mock()->get('Api\Entities\User'), $date);

        $this->assertInstanceOf('DateTime', $result);

        $compareDateTime = new \DateTime($date, $timeZoneRiga);
        $offset = $result->getTimezone()->getOffset($compareDateTime);

        $this->assertEquals(7200, $offset);
    }

    public function testDateIsSetCorrectly()
    {
        $timezone = 'Europe/Berlin';
        $date = '2014-12-30';

        $this->mock()->get('Api\Entities\User')
             ->expects($this->once())
             ->method('getTimezone')
             ->will($this->returnValue($timezone));

        $result = $this->sut->getDateTime($this->mock()->get('Api\Entities\User'), $date);

        $this->assertEquals($date, $result->format('Y-m-d'));
    }

    /**
     * @return array
     */
    public function invalidDateDataProvider()
    {
        return array(
            array('ABC'),
            array('date'),
            array('0'),
            array(''),
            array('2010-10-01 abc'),
            array('abc 2010-10-01'),
        );
    }

    /**
     * @dataProvider invalidDateDataProvider
     *
     * @expectedException \InvalidArgumentException
     *
     * @param string $date
     */
    public function testWrongDateData($date)
    {
        $this->sut->getDateTime($this->mock()->get('Api\Entities\User'), $date);
    }
}
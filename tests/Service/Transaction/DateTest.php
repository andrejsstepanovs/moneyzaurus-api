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
        $timezone = 'Europe/Riga';
        $date = '2015-12-30';

        $this->mock()->get('Api\Entities\User')
            ->expects($this->once())
            ->method('getTimezone')
            ->will($this->returnValue($timezone));

        $result = $this->sut->getDateTime($this->mock()->get('Api\Entities\User'), $date);

        $this->assertInstanceOf('DateTime', $result);

        $location = $result->getTimezone()->getLocation();
        $this->assertEquals($location['country_code'], 'LV');
    }

    public function testDateIsSetCorrectly()
    {
        $timezone = 'Europe/Berlin';
        $date = '2015-12-30';

        $this->mock()->get('Api\Entities\User')
             ->expects($this->once())
             ->method('getTimezone')
             ->will($this->returnValue($timezone));

        $result = $this->sut->getDateTime($this->mock()->get('Api\Entities\User'), $date);

        $this->assertEquals($date, $result->format('Y-m-d'));
    }
}
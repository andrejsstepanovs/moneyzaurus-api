<?php

namespace Tests\Service;

use Api\Service\Locale;
use Tests\TestCase;

/**
 * Class LocatorTest
 *
 * @package Tests\Service
 */
class LocaleTest extends TestCase
{
    /** @var Locale */
    private $sut;

    public function setUp()
    {
        $this->sut = new Locale();
    }

    /**
     * @return array
     */
    public function localesDataProvider()
    {
        return include 'fixtures/locales.php';
    }

    /**
     * @dataProvider localesDataProvider
     *
     * @param string $expected
     */
    public function testSetLocale($expected)
    {
        $actual = $this->sut->setLocale($expected)->getLocale();
        $this->assertEquals($expected, $actual);
    }

    public function testSetUser()
    {
        $locale   = 'en_EN';
        $timezone = 'Europe/Berlin';

        $user = $this->mock()->get('Api\Entities\User');
        $user->expects($this->once())
             ->method('getLocale')
             ->will($this->returnValue($locale));

        $user->expects($this->once())
             ->method('getTimezone')
             ->will($this->returnValue($timezone));

        $this->sut->setUser($user);

        $this->assertEquals($locale, $this->sut->getLocale());
        $this->assertEquals($timezone, $this->sut->getTimezone());
    }

    public function testGetDateFormatter()
    {
        $locale   = 'de_DE';
        $timezone = 'Europe\Berlin';
        $actual = $this->sut->setLocale($locale)->setTimezone($timezone)->getDateFormatter();

        $this->assertInstanceOf('IntlDateFormatter', $actual);
        $this->assertEquals('dd.MM.yy', $actual->getPattern());
    }

    public function testGetDateTimeFormatter()
    {
        $locale   = 'de_DE';
        $timezone = 'Europe\Berlin';
        $actual = $this->sut->setLocale($locale)->setTimezone($timezone)->getDateTimeFormatter();

        $this->assertInstanceOf('IntlDateFormatter', $actual);
        $this->assertEquals('dd.MM.yy HH:mm:ss', $actual->getPattern());
    }

    /**
     * @return array
     */
    public function formattedMoneyDataProvider()
    {
        return include 'fixtures/formattedMoney.php';
    }

    /**
     * @dataProvider formattedMoneyDataProvider
     *
     * @param string $locale
     * @param string $currency
     * @param int    $amount
     * @param string $expected
     */
    public function testGetFormattedMoney($locale, $currency, $amount, $expected)
    {
        $actual = $this->sut->setLocale($locale)->getFormattedMoney($currency, $amount);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \SebastianBergmann\Money\InvalidArgumentException
     */
    public function testGetFormattedMoneyWIthWrongCurrencyReturnsException()
    {
        $this->sut->setLocale('de_DE')->getFormattedMoney('', 1);
    }

    /**
     * @expectedException \SebastianBergmann\Money\InvalidArgumentException
     */
    public function testGetFormattedMoneyWIthWrongAmountReturnsException()
    {
        $this->sut->setLocale('de_DE')->getFormattedMoney('EUR', '1');
    }

    public function getLanguageDataProvider()
    {
        return array(
            array('lt', 'lt'),
            array('de_DE', 'de'),
            array('DE_DE', 'de'),
            array('lv_LV', 'lv'),
            array('unknown', 'unknown'),
            array('', 'en'),
        );
    }

    /**
     * @dataProvider getLanguageDataProvider
     *
     * @param string $locale
     * @param string $expected
     */
    public function testGetLanguage($locale, $expected)
    {
        $response = $this->sut->setLocale($locale)->getLanguage();

        $this->assertEquals($expected, $response);
    }

    public function getDisplayLanguageProvider()
    {
        return array(
            array('lt', array('Lietuvių')),
            array('de_DE', array('Deutsch')),
            array('DE_DE', array('Deutsch')),
            array('lv_LV', array('Latviešu')),
            array('unknown', array('Unknown')),
            array('', array('English', 'En')),
        );
    }

    /**
     * @dataProvider getDisplayLanguageProvider
     *
     * @param string $locale
     * @param array  $expected
     */
    public function testGetDisplayLanguage($locale, $expected)
    {
        $response = $this->sut->setLocale($locale)->getDisplayLanguage();

        $this->assertTrue(in_array($response, $expected));
    }

    public function getRegionProvider()
    {
        return array(
            array('lt_LT', 'LT'),
            array('lt', ''),
            array('de_DE', 'DE'),
            array('DE_DE', 'DE'),
            array('lv_LV', 'LV'),
            array('unknown', ''),
            array('', 'US'),
        );
    }

    /**
     * @dataProvider getRegionProvider
     *
     * @param string $locale
     * @param string $expected
     */
    public function testGetRegion($locale, $expected)
    {
        $response = $this->sut->setLocale($locale)->getRegion();

        $this->assertEquals($expected, $response);
    }

    public function isValidLocaleProvider()
    {
        return array(
            array('lt_LT', true),
            array('lt', false),
            array('de_DE', true),
            array('DE_DE', true),
            array('lv_LV', true),
            array('unknown', false),
            array('', false),
        );
    }

    /**
     * @dataProvider isValidLocaleProvider
     *
     * @param string $locale
     * @param string $expected
     */
    public function testIsValidLocale($locale, $expected)
    {
        $response = $this->sut->setLocale($locale)->isValidLocale();

        $this->assertEquals($expected, $response);
    }

    public function isValidTimezoneProvider()
    {
        return array(
            array('', false),
            array('Europe', false),
            array('Europe/Berlin', true),
            array('Europe/Riga', true),
        );
    }

    /**
     * @dataProvider isValidTimezoneProvider
     *
     * @param string $timezone
     * @param string $expected
     */
    public function testIsValidTimezone($timezone, $expected)
    {
        $response = $this->sut->setTimezone($timezone)->isValidTimezone();

        $this->assertEquals($expected, $response);
    }
}

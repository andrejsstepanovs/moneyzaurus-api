<?php

namespace Tests\Controller\Transactions;

use Api\Controller\Transactions\CreateController;
use Tests\TestCase;

/**
 * Class CreateControllerTest
 *
 * @package Tests\Controller\Transactions
 */
class CreateControllerTest extends TestCase
{
    /** @var CreateController */
    private $sut;

    public function setUp()
    {
        $this->sut = new CreateController();
        $this->sut->setSave($this->mock()->get('Api\Service\Transaction\Save'));
        $this->sut->setMoney($this->mock()->get('Api\Service\Transaction\Money'));
        $this->sut->setDate($this->mock()->get('Api\Service\Transaction\Date'));
    }

    public function testIfMissingTimezoneTransactionWillNotCreated()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $price    = '10.00';
        $currency = 'EUR';
        $date     = '2014-12-30';

        $this->mock()->get('Api\Service\Transaction\Save')
             ->expects($this->never())
             ->method('save');

        $this->mock()->get('Api\Service\Transaction\Date')
             ->expects($this->once())
             ->method('getDateTime')
             ->will($this->throwException(new \RuntimeException('TEST')));

        $response = $this->sut->getResponse($user, $item, $group, $price, $currency, $date);

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testSuccessfullyCreateTransaction()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $price    = '10.00';
        $currency = 'EUR';
        $date     = '2014-12-30';

        $this->mock()->get('Api\Service\Transaction\Date')
             ->expects($this->once())
             ->method('getDateTime')
             ->will($this->returnValue(new \DateTime($date)));

        $this->mock()->get('Api\Service\Transaction\Save')
             ->expects($this->once())
             ->method('save')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Entities\User')
            ->expects($this->any())
            ->method('getTimezone')
            ->will($this->returnValue('Europe/Berlin'));

        $response = $this->sut->getResponse($user, $item, $group, $price, $currency, $date);

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertArrayHasKey('id', $response['data']);
    }

    public function testCreateTransactionWithEmptyItem()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $item     = '';
        $group    = 'group';
        $price    = '10.00';
        $currency = 'EUR';
        $date     = '2014-12-30';

        $response = $this->sut->getResponse($user, $item, $group, $price, $currency, $date);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
    }

    public function testCreateTransactionWithEmptyGroup()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = '';
        $price    = '10.00';
        $currency = 'EUR';
        $date     = '2014-12-30';

        $response = $this->sut->getResponse($user, $item, $group, $price, $currency, $date);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
    }

    public function testCreateTransactionWithEmptyCurrency()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $price    = '10.00';
        $currency = '';
        $date     = '2014-12-30';

        $response = $this->sut->getResponse($user, $item, $group, $price, $currency, $date);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
    }

    public function testCreateTransactionWithEmptyDate()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $price    = '10.00';
        $currency = 'EUR';
        $date     = '';

        $response = $this->sut->getResponse($user, $item, $group, $price, $currency, $date);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
    }

    public function testCreateTransactionWithEmptyPrice()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $price    = '';
        $currency = 'EUR';
        $date     = '2010-01-01';

        $response = $this->sut->getResponse($user, $item, $group, $price, $currency, $date);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
    }

}
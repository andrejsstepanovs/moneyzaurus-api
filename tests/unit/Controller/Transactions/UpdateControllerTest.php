<?php

namespace Tests\Controller\Transactions;

use Api\Controller\Transactions\UpdateController;
use Tests\TestCase;

/**
 * Class UpdateControllerTest
 *
 * @package Tests\Controller\Transactions
 */
class UpdateControllerTest extends TestCase
{
    /** @var UpdateController */
    private $sut;

    public function setUp()
    {
        $this->sut = new UpdateController();
        $this->sut->setSave($this->mock()->get('Api\Service\Transaction\Save'));
        $this->sut->setValidate($this->mock()->get('Api\Service\Transaction\Validate'));
        $this->sut->setMoney($this->mock()->get('Api\Service\Transaction\Money'));
        $this->sut->setDate($this->mock()->get('Api\Service\Transaction\Date'));
        $this->sut->setData($this->mock()->get('Api\Service\Transaction\Data'));
    }

    public function testIfTransactionIsNotFound()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $connectedUserIds = array();
        $transactionId = 123;
        $item     = 'item';
        $group    = 'group';
        $price    = '10.00';
        $currency = 'EUR';
        $date     = '2014-12-30';

        $this->mock()->get('Api\Service\Transaction\Save')
             ->expects($this->never())
             ->method('save');

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue(null));

        $response = $this->sut->getResponse(
            $user,
            $connectedUserIds,
            $transactionId,
            $item,
            $group,
            $price,
            $currency,
            $date
        );

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testTransactionFoundButNotAllowed()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $connectedUserIds = array();
        $transactionId = 123;
        $item     = 'item';
        $group    = 'group';
        $price    = '10.00';
        $currency = 'EUR';
        $date     = '2014-12-30';

        $this->mock()->get('Api\Service\Transaction\Save')
             ->expects($this->never())
             ->method('save');

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(false));

        $response = $this->sut->getResponse(
            $user,
            $connectedUserIds,
            $transactionId,
            $item,
            $group,
            $price,
            $currency,
            $date
        );

        $this->assertFalse($response['success']);
    }

    public function testSuccessfulSaveWillReturnSuccessTrue()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $connectedUserIds = array();
        $transactionId = 123;
        $item     = null;
        $group    = null;
        $price    = null;
        $currency = null;
        $date     = null;

        $this->mock()->get('Api\Service\Transaction\Save')
             ->expects($this->once())
             ->method('save')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->any())
             ->method('getDate')
             ->will($this->returnValue(new \DateTime));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->any())
             ->method('getItem')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->any())
             ->method('getGroup')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->any())
             ->method('getCurrency')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $response = $this->sut->getResponse(
            $user,
            $connectedUserIds,
            $transactionId,
            $item,
            $group,
            $price,
            $currency,
            $date
        );

        $this->assertTrue($response['success']);
    }

    public function testSuccessfulSaveWithOnlyProvidedData()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $connectedUserIds = array();
        $transactionId = 123;
        $item     = 'item';
        $group    = 'group';
        $price    = 'price';
        $currency = 'EUR';
        $date     = '2012-10-12 12:00:00';

        $this->mock()->get('Api\Service\Transaction\Save')
             ->expects($this->once())
             ->method('save')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Transaction\Date')
            ->expects($this->once())
            ->method('getDateTime')
            ->will($this->returnValue(new \DateTime()));

        $response = $this->sut->getResponse(
            $user,
            $connectedUserIds,
            $transactionId,
            $item,
            $group,
            $price,
            $currency,
            $date
        );

        $this->assertTrue($response['success']);
    }

    public function testSaveThrowsExceptionWillReturnSuccessFalse()
    {
        $user = $this->mock()->get('Api\Entities\User');
        $connectedUserIds = array();
        $transactionId = 123;
        $item     = 'item';
        $group    = 'group';
        $price    = 'price';
        $currency = 'EUR';
        $date     = '2012-10-12 12:00:00';

        $this->mock()->get('Api\Service\Transaction\Save')
             ->expects($this->once())
             ->method('save')
             ->will($this->throwException(new \RuntimeException('TEST')));

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Transaction\Date')
             ->expects($this->once())
             ->method('getDateTime')
             ->will($this->returnValue(new \DateTime()));

        $response = $this->sut->getResponse(
            $user,
            $connectedUserIds,
            $transactionId,
            $item,
            $group,
            $price,
            $currency,
            $date
        );

        $this->assertFalse($response['success']);
        $this->assertEquals('TEST', $response['message']);
    }
}
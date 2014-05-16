<?php

namespace Tests\Controller\Transactions;

use Api\Controller\Transactions\IdController;
use Tests\TestCase;

/**
 * Class CreateControllerTest
 *
 * @package Tests\Controller\Transactions
 */
class IdControllerTest extends TestCase
{
    /** @var IdController */
    private $sut;

    public function setUp()
    {
        $this->sut = new IdController();
        $this->sut->setMoney($this->mock()->get('Api\Service\Transaction\Money'));
        $this->sut->setData($this->mock()->get('Api\Service\Transaction\Data'));
        $this->sut->setLocale($this->mock()->get('Api\Service\Locale'));
        $this->sut->setValidate($this->mock()->get('Api\Service\Transaction\Validate'));

        $this->mock()->get('Api\Entities\Transaction')
            ->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Entities\Transaction')
            ->expects($this->any())
            ->method('getItem')
            ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->mock()->get('Api\Entities\Transaction')
            ->expects($this->any())
            ->method('getGroup')
            ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));
    }

    /**
     * @return \IntlDateFormatter
     */
    private function getIntlDateFormatterStub()
    {
        return new \IntlDateFormatter(
            'Europe/Berlin',
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE
        );
    }

    /**
     * @return \DateTime
     */
    private function getDateTimeStub()
    {
        return new \DateTime('2015-01-02 10:00:59');
    }

    public function testTransactionIdNotFound()
    {
        $connectedUserIds = array();
        $transactionId = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($transactionId))
            ->will($this->returnValue(null));

        $response = $this->sut->getResponse(
            $this->mock()->get('Api\Entities\User'),
            $connectedUserIds,
            $transactionId
        );

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testTransactionFoundButNotAllowed()
    {
        $connectedUserIds = array();
        $transactionId = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
            ->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $transactionId);

        $this->assertFalse($response['success']);
    }

    public function testSuccessfulId()
    {
        $connectedUserIds = array();
        $transactionId = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('setLocale')
             ->will($this->returnSelf());

        $this->mock()->get('Api\Entities\Transaction')
            ->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->mock()->get('Api\Entities\Transaction')
            ->expects($this->once())
            ->method('getDate')
            ->will($this->returnValue($this->getDateTimeStub()));

        $this->mock()->get('Api\Entities\Transaction')
            ->expects($this->once())
            ->method('getDateCreated')
            ->will($this->returnValue($this->getDateTimeStub()));

        $this->mock()->get('Api\Entities\Currency')
             ->expects($this->any())
             ->method('getCurrency')
             ->will($this->returnValue('EUR'));

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('getDateFormatter')
            ->will($this->returnValue($this->getIntlDateFormatterStub()));

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('getDateTimeFormatter')
            ->will($this->returnValue($this->getIntlDateFormatterStub()));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $transactionId);

        $this->assertTrue($response['success']);
        $this->assertEquals(
            array(
                'success'           => true,
                'id'                => null,
                'item'              => null,
                'group'             => null,
                'amount'            => null,
                'price'             => 0.00,
                'money'             => null,
                'currency'          => 'EUR',
                'date'              => '2015-01-02',
                'date_full'         => '2015-01-02',
                'date_timestamp'    => 1420192859,
                'created'           => '2015-01-02',
                'created_full'      => '2015-01-02',
                'created_timestamp' => 1420192859,
                'user'              => null,
                'email'             => null
            ),
            $response
        );
    }

}

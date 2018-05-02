<?php

namespace Tests\Service\Transaction;

use Api\Service\Transaction\Data;
use Tests\TestCase;

/**
 * Class TransactionsTest
 *
 * @package Tests
 */
class DataTest extends TestCase
{
    /** @var Data */
    private $sut;

    public function setUp()
    {
        $this->sut = new Data();
        $this->sut->setLocale($this->mock()->get('Api\Service\Locale'));
        $this->sut->setEntityManager($this->mock()->get('Doctrine\ORM\EntityManager'));
        $this->sut->setTransactionEntity($this->mock()->get('Doctrine\ORM\EntityRepository'));
    }

    public function testGetTransactionsListQuery()
    {
        $expectedResponse = array('data1', 'data2', 'data3');
        $this->mock()->get('Doctrine\ORM\AbstractQuery')
             ->expects($this->once())
             ->method('getResult')
             ->will($this->returnValue($expectedResponse));

        $this->mock()->get('Doctrine\ORM\AbstractQuery')
             ->expects($this->any())
             ->method('setMaxResults')
             ->with($this->equalTo(1));

        $this->mock()->get('Doctrine\ORM\AbstractQuery')
             ->expects($this->any())
             ->method('setFirstResult')
             ->with($this->equalTo(3));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('createQuery')
             ->with(
                 $this->callback(
                      function ($dql) {
                          $searchWords = array(
                              't.date >= :dateFrom',
                              't.date <= :dateTill',
                              'i.name LIKE :item',
                              'g.name LIKE :group',
                              't.price LIKE :price',
                          );
                          $matches = 0;

                          foreach ($searchWords as $word) {
                              if (strpos($dql, $word) !== false) {
                                  $matches++;
                              }
                          }

                          return $matches == count($searchWords);
                      }
                 )
             )
             ->will($this->returnValue($this->mock()->get('Doctrine\ORM\AbstractQuery')));

        $userIds = array();
        $offset = 3;
        $limit = null;
        $dateFrom = new \DateTime('2099-01-01');
        $dateTill = new \DateTime('2099-01-01');
        $item = 'item';
        $group = 'group';
        $price = 'price';

        $result = $this->sut->getTransactionsList(
            $userIds,
            $offset,
            $limit,
            $dateFrom,
            $dateTill,
            $item,
            $group,
            $price
        );

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @return array
     */
    public function normalizeResultsDataProvider()
    {
        return include __DIR__ . '/fixtures/normalizeTransactionResults.php';
    }

    /**
     * @dataProvider normalizeResultsDataProvider
     *
     * @param array $transactions
     * @param array $expected
     */
    public function testNormalizeResults(array $transactions, array $expected)
    {
        $localeMock = $this->mock()->get('Api\Service\Locale');
        $localeMock->expects($this->any())->method('setLocale')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getLocale')->will($this->returnValue($expected[0]['locale']));
        $localeMock->expects($this->any())->method('getDateFormatter')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getDateTimeFormatter')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getFormattedMoney')->will($this->returnValue('€ 123,45'));
        $localeMock->expects($this->any())->method('format')->will($this->returnValue('2999-01-01 99:99:99'));

        $user = $this->mock()->get('Api\Entities\User');
        $user->expects($this->once())
             ->method('getLocale')
             ->will($this->returnValue($localeMock));

        $result = $this->sut->normalizeResults($transactions, $user);

        $this->assertEquals($expected, $result);
    }

    public function testFindByIdDidNotFoundResults()
    {
        $transactionId = 123;

        $this->mock()->get('Doctrine\ORM\EntityRepository')
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($transactionId))
            ->will($this->returnValue(null));

        $response = $this->sut->find($transactionId);

        $this->assertNull($response);
    }

    public function testFindByIdWillReturnTransaction()
    {
        $transactionId = 123;

        $this->mock()->get('Doctrine\ORM\EntityRepository')
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($transactionId))
            ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $response = $this->sut->find($transactionId);

        $this->assertInstanceOf(get_class($this->mock()->get('Api\Entities\Transaction')), $response);
    }

    /**
     * @return \DateTime
     */
    private function getDateTimeStub()
    {
        return new \DateTime('2014-01-02 10:00:59');
    }

    public function testToArray()
    {
        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->any())
             ->method('getItem')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->any())
             ->method('getGroup')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->once())
             ->method('getCurrency')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->once())
             ->method('getUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->once())
             ->method('getDate')
             ->will($this->returnValue($this->getDateTimeStub()));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->once())
             ->method('getPrice')
             ->will($this->returnValue(100));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->once())
             ->method('getId')
             ->will($this->returnValue(12345));

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->once())
             ->method('getDateCreated')
             ->will($this->returnValue($this->getDateTimeStub()));

        $this->mock()->get('Api\Entities\Currency')
             ->expects($this->any())
             ->method('getCurrency')
             ->will($this->returnValue('EUR'));

        $response = $this->sut->toArray($this->mock()->get('Api\Entities\Transaction'));

        $expected = array(
            'id'              => 12345,
            'dateTransaction' => $this->getDateTimeStub(),
            'dateCreated'     => $this->getDateTimeStub(),
            'amount'          => 100,
            'currency'        => 'EUR',
            'currencyName'    => null,
            'currencySymbol'  => null,
            'email'           => null,
            'role'            => null,
            'userId'          => null,
            'locale'          => null,
            'timezone'        => null,
            'userName'        => null,
            'itemName'        => null,
            'itemId'          => null,
            'groupName'       => null,
            'groupId'         => null,
        );

        $this->assertTrue(is_array($response));
        $this->assertEquals($expected, $response);
    }
}

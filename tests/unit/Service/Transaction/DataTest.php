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

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('createQuery')
             ->with(
                 $this->callback(
                      function($dql) {
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
        $offset = 1;
        $limit = 1;
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
        $localeMock->expects($this->any())->method('getDateFormatter')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getDateTimeFormatter')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getFormattedMoney')->will($this->returnValue('€ 123,45'));
        $localeMock->expects($this->any())->method('format')->will($this->returnValue('2999-01-01 99:99:99'));

        $result = $this->sut->normalizeResults($transactions);

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

}
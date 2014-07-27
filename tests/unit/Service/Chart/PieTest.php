<?php

namespace Tests\Service\Chart;

use Api\Service\Chart\Pie;
use Tests\TestCase;

/**
 * Class PieTest
 *
 * @package Data
 */
class PieTest extends TestCase
{
    /** @var Pie */
    private $sut;

    public function setUp()
    {
        $this->sut = new Pie();
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
                          't.currency = :currency'
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
        $dateFrom = new \DateTime('2099-01-01');
        $dateTill = new \DateTime('2099-01-01');
        $currency = 'EUR';

        $result = $this->sut->getData(
            $userIds,
            $currency,
            $dateFrom,
            $dateTill
        );

        $this->assertEquals($expectedResponse, $result);
    }

    public function testNormalizeResults()
    {
        $data     = array(
            array(
                'amount' => 12345
            )
        );
        $expected = array(
            array(
                'amount' => 12345,
                'price'  => 123.45,
                'money'  => '€ 123,45'
            )
        );
        $currency = 'EUR';

        $localeMock = $this->mock()->get('Api\Service\Locale');
        $localeMock->expects($this->any())->method('setLocale')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getDateFormatter')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getDateTimeFormatter')->will($this->returnSelf());
        $localeMock->expects($this->any())->method('getFormattedMoney')->will($this->returnValue('€ 123,45'));

        $user = $this->mock()->get('Api\Entities\User');
        $user->expects($this->once())->method('getLocale')->will($this->returnValue('en_EN'));
        $user->expects($this->once())->method('getTimezone')->will($this->returnValue('Europe/Berlin'));

        $result = $this->sut->normalizeResults($data, $user, $currency);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function sortByPercentDataProvider()
    {
        return include __DIR__ . '/fixtures/sort_by_precent.php';
    }

    /**
     * @dataProvider sortByPercentDataProvider
     *
     * @param array $data
     * @param array $expected
     */
    public function testSortByPercent(array $data, array $expected)
    {
        $response = $this->sut->sortByPercent($data);

        $this->assertEquals($expected, $response);
    }

    /**
     * @return array
     */
    public function addPercentDataProvider()
    {
        return include __DIR__ . '/fixtures/add_percent.php';
    }

    /**
     * @dataProvider addPercentDataProvider
     *
     * @param array $data
     * @param array $expected
     */
    public function testAddPercent(array $data, array $expected)
    {
        $response = $this->sut->addPercent($data);

        $this->assertEquals($expected, $response);
    }
}
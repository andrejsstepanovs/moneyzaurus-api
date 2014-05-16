<?php

namespace Tests\Service\Transaction;

use Api\Service\Predict\Price as PredictPrice;
use Tests\TestCase;

/**
 * Class PriceTest
 *
 * @package Tests
 */
class PriceTest extends TestCase
{
    /** @var PredictPrice */
    private $sut;

    public function setUp()
    {
        $this->sut = new PredictPrice();
        $this->sut->setEntityManager($this->mock()->get('Doctrine\ORM\EntityManager'));
    }

    public function testPricePredictionReturnsExpected()
    {
        $userIds = array(1, 2);
        $item = 'item';
        $group = 'group';

        $expectedResponse = array('data1', 'data2', 'data3');
        $queryMock = $this->mock()->get('Doctrine\ORM\AbstractQuery');
        $queryMock
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
                            'i.name LIKE :item',
                            'g.name LIKE :group',
                            't.user IN (:userIds)'
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
            ->will($this->returnValue($queryMock));

        $response = $this->sut->predict($userIds, $item, $group);

        $this->assertEquals($expectedResponse, $response);
    }

}
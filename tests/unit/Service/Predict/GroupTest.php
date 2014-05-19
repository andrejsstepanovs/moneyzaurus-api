<?php

namespace Tests\Service\Transaction;

use Api\Service\Predict\Group as PredictGroup;
use Tests\TestCase;

/**
 * Class GroupTest
 *
 * @package Tests
 */
class GroupTest extends TestCase
{
    /** @var PredictGroup */
    private $sut;

    public function setUp()
    {
        $this->sut = new PredictGroup();
        $this->sut->setEntityManager($this->mock()->get('Doctrine\ORM\EntityManager'));
    }

    public function testGroupPredictionReturnsExpected()
    {
        $userIds = array(1, 2);
        $item = 'item';

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

        $response = $this->sut->predict($userIds, $item);

        $this->assertEquals($expectedResponse, $response);
    }

}
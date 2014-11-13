<?php

namespace Tests\Controller\Transactions;

use Api\Controller\Transactions\RemoveController;
use Tests\TestCase;

/**
 * Class RemoveControllerTest
 *
 * @package Tests\Controller\Transactions
 */
class RemoveControllerTest extends TestCase
{
    /** @var RemoveController */
    private $sut;

    public function setUp()
    {
        $this->sut = new RemoveController();
        $this->sut->setData($this->mock()->get('Api\Service\Transaction\Data'));
        $this->sut->setValidate($this->mock()->get('Api\Service\Transaction\Validate'));
        $this->sut->setRemove($this->mock()->get('Api\Service\Transaction\Remove'));
    }

    public function testTransactionNotFound()
    {
        $connectedUserIds = array();
        $id = 1;

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $id);

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testTransactionFoundButNotAllowedToRemove()
    {
        $connectedUserIds = array();
        $id = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(false));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $id);

        $this->assertFalse($response['success']);
    }

    public function testRemoveFailedWillReturnFailure()
    {
        $connectedUserIds = array();
        $id = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Transaction\Remove')
             ->expects($this->once())
             ->method('remove')
             ->will($this->returnValue(false));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $id);

        $this->assertFalse($response['success']);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRemoveThrowExceptionWillReturnFailure()
    {
        $connectedUserIds = array();
        $id = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Transaction\Remove')
             ->expects($this->once())
             ->method('remove')
             ->will($this->throwException(new \RuntimeException('TEST')));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $id);

        $this->assertFalse($response['success']);
    }

    public function testSuccessfullRemove()
    {
        $connectedUserIds = array();
        $id = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Transaction\Remove')
             ->expects($this->once())
             ->method('remove')
             ->will($this->returnValue(true));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $id);

        $this->assertTrue($response['success']);
    }
}

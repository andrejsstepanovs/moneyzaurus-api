<?php

namespace Tests\Service\Transaction;

use Api\Service\Transaction\Remove;
use Tests\TestCase;

/**
 * Class RemoveTest
 *
 * @package Tests
 */
class RemoveTest extends TestCase
{
    /** @var Remove */
    private $sut;

    public function setUp()
    {
        $this->sut = new Remove();
        $this->sut->setEntityManager($this->mock()->get('Doctrine\ORM\EntityManager'));
    }

    public function testSuccessfulRemove()
    {
        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('beginTransaction');

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('remove')
             ->with($this->isInstanceOf($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('flush');

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('commit');

        $response = $this->sut->remove($this->mock()->get('Api\Entities\Transaction'));

        $this->assertTrue($response);
    }

    public function testOnSaveFailureShouldTriggerTransactionRollback()
    {
        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('commit')
             ->will($this->throwException(new \Exception('TEST')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('rollback');

        $response = $this->sut->remove($this->mock()->get('Api\Entities\Transaction'));

        $this->assertFalse($response);
    }
}

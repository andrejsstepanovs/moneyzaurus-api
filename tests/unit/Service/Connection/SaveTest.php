<?php

namespace Tests\Service\Connection;

use Api\Service\Connection\Save;
use Tests\TestCase;

/**
 * Class SaveTest
 *
 * @package Tests
 */
class SaveTest extends TestCase
{
    /** @var Save */
    private $sut;

    public function setUp()
    {
        $this->sut = new Save();
        $this->sut->setEntityManager($this->mock()->get('Doctrine\ORM\EntityManager'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSaveCommitThrowsExceptionWillRollBackTransaction()
    {
        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('commit')
            ->will($this->throwException(new \RuntimeException('TEST')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('rollback');

        $this->sut->save($this->mock()->get('Api\Entities\Connection'));
    }

    public function testSuccessfulSave()
    {
        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->never())
             ->method('rollback');

        $response = $this->sut->save($this->mock()->get('Api\Entities\Connection'));

        $this->assertInstanceOf(get_class($this->mock()->get('Api\Entities\Connection')), $response);
    }
}
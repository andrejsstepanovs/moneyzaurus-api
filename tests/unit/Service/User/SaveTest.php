<?php

namespace Tests\Service\User;

use Api\Service\User\Save;
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
    public function testSaveWillThrowExceptionRollbackWillBeTriggered()
    {
        $this->mock()
            ->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('commit')
            ->will($this->throwException(new \RuntimeException('TEST')));

        $this->mock()
             ->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('rollback');

        $this->sut->saveUser($this->mock()->get('Api\Entities\User'));
    }

    public function testSaveSuccessfullyWillReturnUserEntity()
    {
        $this->mock()
             ->get('Doctrine\ORM\EntityManager')
             ->expects($this->never())
             ->method('rollback');

        $response = $this->sut->saveUser($this->mock()->get('Api\Entities\User'));

        $this->assertInstanceOf(get_class($this->mock()->get('Api\Entities\User')), $response);
    }
}
<?php

namespace Tests\Service\User;

use Api\Service\User\Data;
use Tests\TestCase;

/**
 * Class DataTest
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
        $this->sut->setUser($this->mock()->get('Doctrine\ORM\EntityRepository'));
    }

    public function testUserNotFoundWillReturnNull()
    {
        $username = 'username';

        $response = $this->sut->findUser($username);
        $this->assertNull($response);
    }

    public function testUserFoundWillReturnUser()
    {
        $username = 'username';

        $this->mock()
             ->get('Doctrine\ORM\EntityRepository')
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $response = $this->sut->findUser($username);

        $this->assertInstanceOf(get_class($this->mock()->get('Api\Entities\User')), $response);
    }

}
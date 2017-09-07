<?php

namespace Tests\Service\Transaction;

use Doctrine\ORM\EntityRepository;
use Api\Service\Transaction\Save;
use Api\Entities\Item;
use Api\Entities\Group;
use Api\Entities\Currency;
use Api\Entities\Transaction;
use Tests\TestCase;

/**
 * Class CreateTest
 *
 * @package Tests
 */
class CreateTest extends TestCase
{
    /** @var Save */
    private $sut;

    /** @var EntityRepository|Item|\PHPUnit_Framework_MockObject_MockObject */
    private $itemRepositoryMock;

    /** @var EntityRepository|Group|\PHPUnit_Framework_MockObject_MockObject */
    private $groupRepositoryMock;

    /** @var EntityRepository|Currency|\PHPUnit_Framework_MockObject_MockObject */
    private $currencyRepositoryMock;

    public function setUp()
    {
        $this->sut = new Save();
        $this->sut->setEntityManager($this->mock()->get('Doctrine\ORM\EntityManager'));
        $this->sut->setItemEntity($this->getItemEntityRepositoryMock());
        $this->sut->setGroupEntity($this->getGroupEntityRepositoryMock());
        $this->sut->setCurrencyEntity($this->getCurrencyEntityRepositoryMock());
    }

    /**
     * @return EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEntityRepositoryMock()
    {
        $methods = array('findOneBy');

        return $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @return EntityRepository|Item|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getItemEntityRepositoryMock()
    {
        if ($this->itemRepositoryMock === null) {
            $this->itemRepositoryMock = $this->getEntityRepositoryMock();
        }

        return $this->itemRepositoryMock;
    }

    /**
     * @return EntityRepository|Group|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getGroupEntityRepositoryMock()
    {
        if ($this->groupRepositoryMock === null) {
            $this->groupRepositoryMock = $this->getEntityRepositoryMock();
        }

        return $this->groupRepositoryMock;
    }

    /**
     * @return EntityRepository|Currency|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getCurrencyEntityRepositoryMock()
    {
        if ($this->currencyRepositoryMock === null) {
            $this->currencyRepositoryMock = $this->getEntityRepositoryMock();
        }

        return $this->currencyRepositoryMock;
    }

    /**
     * @return Transaction
     */
    private function getTransactionStub()
    {
        return new Transaction();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateCurrencyIsMissing()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 100;
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->getCurrencyEntityRepositoryMock()
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('rollback');

        $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testItemCannotBeCreated()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 100;
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->getCurrencyEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('persist')
             ->will($this->throwException(new \RuntimeException('TEST')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('rollback');

        $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testItemFoundButGroupCannotBeCreated()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 100;
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->getCurrencyEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->getItemEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('persist')
             ->will($this->throwException(new \RuntimeException('TEST')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('rollback');

        $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);
    }

    /**
     * @expectedException \SebastianBergmann\Money\InvalidArgumentException
     */
    public function testItemAndGroupFoundButMoneyReceivesWrongCurrency()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 100;
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->getCurrencyEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->getItemEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->getGroupEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('rollback');

        $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);
    }

    /**
     * @expectedException \SebastianBergmann\Money\InvalidArgumentException
     */
    public function testItemAndGroupFoundButMoneyReceivesWrongAmountValue()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 'wrong amount value';
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->mock()->get('Api\Entities\Currency')
             ->expects($this->once())
             ->method('getCurrency')
             ->will($this->returnValue('USD'));

        $this->getCurrencyEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->getItemEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->getGroupEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->once())
             ->method('rollback');

        $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);
    }

    public function testSuccessfulTransactionCreate()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 100;
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->mock()->get('Api\Entities\Currency')
             ->expects($this->once())
             ->method('getCurrency')
             ->will($this->returnValue('USD'));

        $this->getCurrencyEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->getItemEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->getGroupEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->never())
             ->method('rollback');

        $response = $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);

        $this->assertInstanceOf('Api\Entities\Transaction', $response);
    }

    public function testItemIsNotFoundAndIsCreatedSuccessfully()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 100;
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->mock()->get('Api\Entities\Currency')
             ->expects($this->once())
             ->method('getCurrency')
             ->will($this->returnValue('USD'));

        $this->getCurrencyEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->getItemEntityRepositoryMock()
             ->expects($this->at(0))
             ->method('findOneBy')
             ->will($this->returnValue(null));

        $this->getItemEntityRepositoryMock()
             ->expects($this->at(1))
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->getGroupEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->never())
             ->method('rollback');

        $response = $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);

        $this->assertInstanceOf('Api\Entities\Transaction', $response);
    }

    public function testGroupIsNotFoundAndIsCreatedSuccessfully()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $item     = 'item';
        $group    = 'group';
        $amount   = 100;
        $currency = 'UNKNOWN';
        $dateTime = new \DateTime();

        $this->mock()->get('Api\Entities\Currency')
             ->expects($this->once())
             ->method('getCurrency')
             ->will($this->returnValue('USD'));

        $this->getCurrencyEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Currency')));

        $this->getItemEntityRepositoryMock()
             ->expects($this->once())
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Item')));

        $this->getGroupEntityRepositoryMock()
             ->expects($this->at(0))
             ->method('findOneBy')
             ->will($this->returnValue(null));

        $this->getGroupEntityRepositoryMock()
             ->expects($this->at(1))
             ->method('findOneBy')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Group')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
             ->expects($this->never())
             ->method('rollback');

        $response = $this->sut->save($this->getTransactionStub(), $user, $item, $group, $amount, $currency, $dateTime);

        $this->assertInstanceOf('Api\Entities\Transaction', $response);
    }
}

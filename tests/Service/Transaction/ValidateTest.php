<?php

namespace Tests\Service\Transaction;

use Api\Entities\User;
use Api\Service\Transaction\Validate;
use Tests\TestCase;

/**
 * Class ValidateTest
 *
 * @package Tests
 */
class ValidateTest extends TestCase
{
    /** @var Validate */
    private $sut;

    public function setUp()
    {
        $this->sut = new Validate;
    }

    /**
     * @return User|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getUserEntityMock()
    {
        $methods = array('getId');
        $userEntity = $this
            ->getMockBuilder('Api\Entities\User')
            ->setMethods($methods)
            ->getMock();

        return $userEntity;
    }

    /**
     * @return array
     */
    public function isAllowedDataProvider()
    {
        return array(
            array(1, array(), 1, true),
            array(1, array(), 2, false),
            array(1, array(2), 2, true),
            array(1, array(2, 3), 3, true),
            array(1, array(2, 3, 4), 3, true),
            array(1, array(2, 3, 4), 5, false),
            array(1, array(2, 3, 4), 1, true),
            array(1, array(3, 4), 2, false),
        );
    }

    /**
     * @dataProvider isAllowedDataProvider
     *
     * @param int   $userId
     * @param array $connectedUserIds
     * @param int   $transactionUserId
     * @param bool  $expected
     */
    public function testIsAllowed(
        $userId,
        array $connectedUserIds,
        $transactionUserId,
        $expected
    ) {
        $transactionUserEntity = $this->getUserEntityMock();
        $userEntity = $this->getUserEntityMock();

        $this->mock()->get('Api\Entities\Transaction')
             ->expects($this->any())
             ->method('getUser')
             ->will($this->returnValue($transactionUserEntity));

        $transactionUserEntity
             ->expects($this->any())
             ->method('getId')
             ->will($this->returnValue($transactionUserId));

        $userEntity
             ->expects($this->any())
             ->method('getId')
             ->will($this->returnValue($userId));

        $result = $this->sut->isAllowed($userEntity, $connectedUserIds, $this->mock()->get('Api\Entities\Transaction'));

        $this->assertEquals($expected, $result);
    }

}
<?php

namespace Tests\Service\Connection;

use Api\Service\Connection\Data;
use Tests\TestCase;

/**
 * Class DataTest
 *
 * @package Data
 */
class DataTest extends TestCase
{
    /** @var Data */
    private $sut;

    public function setUp()
    {
        $this->sut = new Data();
        $this->sut->setEmailValidator($this->mock()->get('\Egulias\EmailValidator\EmailValidator'));
        $this->sut->setUserData($this->mock()->get('Api\Service\User\Data'));
        $this->sut->setLocale($this->mock()->get('Api\Service\Locale'));
        $this->sut->setConnectionRepository($this->mock()->get('Doctrine\ORM\EntityManager'));
    }

    public function testFindByUser()
    {
        $connection = $this->mock()->get('Api\Entities\Connection');

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('findBy')
            ->will($this->returnValue($connection));

        $response = $this->sut->findByUser($this->mock()->get('Api\Entities\User'));

        $this->assertInstanceOf(get_class($connection), $response);
    }

    public function testFindById()
    {
        $connection = $this->mock()->get('Api\Entities\Connection');

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue($connection));

        $connectionId = 1;
        $response = $this->sut->findById($connectionId);

        $this->assertInstanceOf(get_class($connection), $response);
    }

    public function testNormalizeResultsWithNoResultsWillReturnArray()
    {
        $user = $this->mock()->get('Api\Entities\User');

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('setTimezone')
            ->will($this->returnSelf());

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('setLocale')
            ->will($this->returnSelf());

        $response = $this->sut->normalizeResults($user, array());

        $this->assertEquals(array(), $response);
    }

    public function testNormalizeResultsWillReturnExpected()
    {
        $user   = $this->mock()->get('Api\Entities\User');
        $locale = $this->mock()->get('Api\Service\Locale');

        $locale->expects($this->any())
               ->method('setTimezone')
               ->will($this->returnSelf());

        $locale->expects($this->any())
               ->method('setLocale')
               ->will($this->returnSelf());

        $dateTime = new \DateTime('2015-01-01');
        $intlDateFormatter = $formatter = new \IntlDateFormatter(
            'Europe/Berlin',
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE
        );

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('getDateTimeFormatter')
            ->will($this->returnValue($intlDateFormatter));

        $connection = $this->mock()->get('Api\Entities\Connection');
        $connection->expects($this->once())->method('getParent')->will($this->returnValue($user));
        $connection->expects($this->once())->method('getDateCreated')->will($this->returnValue($dateTime));


        $connectins = array($connection);
        $response = $this->sut->normalizeResults($user, $connectins);

        $this->assertEquals(
             array(
                 array(
                    'id'                => null,
                    'email'             => null,
                    'state'             => null,
                    'created'           => '2015-01-01',
                    'created_full'      => '2015-01-01',
                    'created_timestamp' => 1420070400
                 )
             ),
             $response
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvitedUserWithInvalidEmail()
    {
        $email = 'email@email.com';

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->sut->getInvitedUser($email);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetInvitedUserWithUnknownUser()
    {
        $email = 'email@email.com';

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Data')
            ->expects($this->once())
            ->method('findUser')
            ->will($this->returnValue(null));

        $this->sut->getInvitedUser($email);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvitedUserWithParentConnectionAlreadyExists()
    {
        $email = 'email@email.com';

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Data')
            ->expects($this->once())
            ->method('findUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->mock()->get('Api\Entities\Connection')));

        $this->sut->getInvitedUser($email);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvitedUserWithUserConnectionAlreadyExists()
    {
        $email = 'email@email.com';

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Data')
            ->expects($this->once())
            ->method('findUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->at(0))
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $this->mock()->get('Doctrine\ORM\EntityManager')
            ->expects($this->at(1))
            ->method('findOneBy')
            ->will($this->returnValue($this->mock()->get('Api\Entities\Connection')));

        $this->sut->getInvitedUser($email);
    }

    public function testGetInvitedUserWillReturnUser()
    {
        $email = 'email@email.com';

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Data')
            ->expects($this->once())
            ->method('findUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $response = $this->sut->getInvitedUser($email);

        $this->assertInstanceOf(get_class($this->mock()->get('Api\Entities\User')), $response);
    }

}
<?php

namespace Tests\Controller\User;

use Api\Controller\User\UpdateController;
use Tests\TestCase;

/**
 * Class UpdateControllerTest
 *
 * @package Tests\Controller\User
 */
class UpdateControllerTest extends TestCase
{
    /** @var UpdateController */
    private $sut;

    public function setUp()
    {
        $this->sut = new UpdateController();
        $this->sut->setUser($this->mock()->get('Api\Service\User\Save'));
        $this->sut->setEmailValidator($this->mock()->get('\Egulias\EmailValidator\EmailValidator'));
        $this->sut->setLocale($this->mock()->get('Api\Service\Locale'));

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('setLocale')
            ->will($this->returnSelf());
    }

    public function testValidEmailUpdateWillSaveUser()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = 'email@email.com';
        $name     = null;
        $locale   = null;
        $language = null;
        $timezone = null;

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
            ->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($email))
            ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Save')
            ->expects($this->once())
            ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertTrue($response['success']);
    }

    public function testNotValidEmailUpdateWillReturnErrorMessage()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = 'email@email.com';
        $name     = null;
        $locale   = null;
        $language = null;
        $timezone = null;

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
             ->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(false));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->never())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertFalse($response['success']);
        $this->assertEquals('Email is not valid', $response['message']);
    }

    public function testValidLocaleWillReturnTrue()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = null;
        $locale   = 'locale';
        $language = null;
        $timezone = null;

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidLocale')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->once())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertTrue($response['success']);
    }

    public function testNotValidLocaleWillReturnErrorMessage()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = null;
        $locale   = 'locale';
        $language = null;
        $timezone = null;

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidLocale')
             ->will($this->returnValue(false));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->never())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertFalse($response['success']);
        $this->assertEquals('Locale is not valid', $response['message']);
    }

    public function testValidLanguageWillReturnTrue()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = null;
        $locale   = null;
        $language = 'language';
        $timezone = null;

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidLocale')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->once())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertTrue($response['success']);
    }

    public function testNotValidLanguageWillReturnErrorMessage()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = null;
        $locale   = null;
        $language = 'language';
        $timezone = null;

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidLocale')
             ->will($this->returnValue(false));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->never())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertFalse($response['success']);
        $this->assertEquals('Language is not valid', $response['message']);
    }

    public function testValidTimezoneWillReturnTrue()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = null;
        $locale   = null;
        $language = null;
        $timezone = 'timezone';

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidTimezone')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->once())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertTrue($response['success']);
    }

    public function testNotValidTimezoneWillReturnErrorMessage()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = null;
        $locale   = null;
        $language = null;
        $timezone = 'timezone';

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidTimezone')
             ->will($this->returnValue(false));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->never())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertFalse($response['success']);
        $this->assertEquals('Timezone is not valid', $response['message']);
    }

    public function testNothingToUpdateWillReturnFalse()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = null;
        $locale   = null;
        $language = null;
        $timezone = null;

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->never())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertFalse($response['success']);
        $this->assertEquals('Nothing to update', $response['message']);
    }

    public function testUpdateAllValidValuesWillReturnTrue()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = 'email@email.com';
        $name     = 'name';
        $locale   = 'locale';
        $language = 'language';
        $timezone = 'timezone';

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidTimezone')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidLocale')
             ->will($this->returnValue(true));

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
             ->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->once())
             ->method('saveUser');

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertTrue($response['success']);
    }

    public function testSaveUserThrowExceptionWillReturnFalse()
    {
        $user     = $this->mock()->get('Api\Entities\User');
        $email    = null;
        $name     = 'name';
        $locale   = null;
        $language = null;
        $timezone = null;

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->once())
             ->method('saveUser')
             ->will($this->throwException(new \RuntimeException('TEST')));

        $response = $this->sut->getResponse($user, $email, $name, $locale, $language, $timezone);

        $this->assertFalse($response['success']);
        $this->assertEquals('TEST', $response['message']);
    }
}

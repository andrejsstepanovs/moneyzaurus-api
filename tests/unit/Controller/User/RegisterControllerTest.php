<?php

namespace Tests\Controller\User;

use Api\Controller\User\RegisterController;
use Tests\TestCase;

/**
 * Class RegisterControllerTest
 *
 * @package Tests\Controller\User
 */
class RegisterControllerTest extends TestCase
{
    /** @var RegisterController */
    private $sut;

    public function setUp()
    {
        $this->sut = new RegisterController();
        $this->sut->setEmailValidator($this->mock()->get('\Egulias\EmailValidator\EmailValidator'));
        $this->sut->setLocale($this->mock()->get('Api\Service\Locale'));
        $this->sut->setCrypt($this->mock()->get('Api\Service\Authorization\Crypt'));
        $this->sut->setUserData($this->mock()->get('Api\Service\User\Data'));
        $this->sut->setUserSave($this->mock()->get('Api\Service\User\Save'));
        $this->sut->setToken($this->mock()->get('Api\Service\Authorization\Token'));

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('setLocale')
            ->will($this->returnSelf());

        $this->mock()->get('Api\Service\Locale')
            ->expects($this->any())
            ->method('setTimezone')
            ->will($this->returnSelf());
    }

    public function testEmptyPasswordProvided()
    {
        $email       = 'email@email.com';
        $password    = '  ';
        $timezone    = '';
        $displayName = '';
        $language    = '';
        $locale      = '';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue(null));

        $response = $this->sut->getResponse($email, $password, $timezone, $displayName, $language, $locale);

        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertContains('Password', $response['message']);
    }

    public function testUserAlreadyExist()
    {
        $email       = 'email@email.com';
        $password    = 'abc123';
        $timezone    = '';
        $displayName = '';
        $language    = '';
        $locale      = '';

        $this->mock()->get('Api\Service\User\Data')
            ->expects($this->once())
            ->method('findUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $response = $this->sut->getResponse($email, $password, $timezone, $displayName, $language, $locale);

        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertContains('User', $response['message']);
    }

    public function testInvalidEmailProvided()
    {
        $email       = 'email@email.com';
        $password    = 'abc123';
        $timezone    = '';
        $displayName = '';
        $language    = '';
        $locale      = '';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue(null));

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $response = $this->sut->getResponse($email, $password, $timezone, $displayName, $language, $locale);

        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertContains('Email', $response['message']);
    }

    public function testNotValidLocaleProvided()
    {
        $email       = 'email@email.com';
        $password    = 'abc123';
        $timezone    = '';
        $displayName = '';
        $language    = '';
        $locale      = '';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue(null));

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
             ->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->once())
             ->method('isValidLocale')
             ->will($this->returnValue(false));

        $response = $this->sut->getResponse($email, $password, $timezone, $displayName, $language, $locale);

        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertContains('Locale', $response['message']);
    }

    public function testInvalidLanguageProvided()
    {
        $email       = 'email@email.com';
        $password    = 'abc123';
        $timezone    = '';
        $displayName = '';
        $language    = '';
        $locale      = '';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue(null));

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
             ->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->at(0))
             ->method('isValidLocale')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->at(1))
             ->method('isValidLocale')
             ->will($this->returnValue(true));

        $response = $this->sut->getResponse($email, $password, $timezone, $displayName, $language, $locale);

        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertContains('Language', $response['message']);
    }

    public function testInvalidTimezoneProvided()
    {
        $email       = 'email@email.com';
        $password    = 'abc123';
        $timezone    = '';
        $displayName = '';
        $language    = '';
        $locale      = '';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue(null));

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
             ->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidLocale')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidTimezone')
             ->will($this->returnValue(false));

        $response = $this->sut->getResponse($email, $password, $timezone, $displayName, $language, $locale);

        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertContains('Timezone', $response['message']);
    }

    public function testSuccessfulRegistrationWillSaveUser()
    {
        $email       = 'email@email.com';
        $password    = 'abc123';
        $timezone    = '';
        $displayName = '';
        $language    = '';
        $locale      = '';

        $this->mock()->get('Api\Service\User\Data')
             ->expects($this->once())
             ->method('findUser')
             ->will($this->returnValue(null));

        $this->mock()->get('\Egulias\EmailValidator\EmailValidator')
             ->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidLocale')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Locale')
             ->expects($this->any())
             ->method('isValidTimezone')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\User\Save')
             ->expects($this->once())
             ->method('saveUser')
             ->will($this->returnValue($this->mock()->get('Api\Entities\User')));

        $response = $this->sut->getResponse($email, $password, $timezone, $displayName, $language, $locale);

        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertNotEmpty($response['data']);
    }
}

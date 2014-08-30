<?php

namespace Api\Controller\User;

use Api\Entities\User;
use Api\Service\Authorization\Token;
use Api\Service\Authorization\Crypt as ModuleCrypt;
use Api\Service\User\Data as UserData;
use Api\Service\User\Save as UserSave;
use Api\Service\Locale;
use Egulias\EmailValidator\EmailValidator;
use Api\Service\AccessorTrait;

/**
 * Class RegisterController
 *
 * @package Api\Controller\User
 *
 * @method RegisterController setCrypt(ModuleCrypt $crypt)
 * @method RegisterController setUserData(UserData $userData)
 * @method RegisterController setUserSave(UserSave $userSave)
 * @method RegisterController setToken(Token $token)
 * @method RegisterController setEmailValidator(EmailValidator $emailValidator)
 * @method RegisterController setLocale(Locale $locale)
 * @method ModuleCrypt        getCrypt()
 * @method UserData           getUserData()
 * @method UserSave           getUserSave()
 * @method Token              getToken()
 * @method EmailValidator     getEmailValidator()
 * @method Locale             getLocale()
 */
class RegisterController
{
    use AccessorTrait;

    /**
     * @param string $email
     * @param string $password
     * @param string $timezone
     * @param string $displayName
     * @param string $language
     * @param string $locale
     *
     * @return array
     */
    public function getResponse(
        $email,
        $password,
        $timezone,
        $displayName,
        $language,
        $locale
    ) {
        $response = array('success' => false);
        $password = trim($password);

        try {
            if ($this->getUserData()->findUser($email)) {
                throw new \RuntimeException('Failed to register new User');
            }

            $user = $this
                ->validateParams($email, $password, $timezone, $language, $locale)
                ->getUser($email, $password, $timezone, $language, $locale, $displayName);

            $user = $this->getUserSave()->saveUser($user);
        } catch (\Exception $exc) {
            $response['message'] = $exc->getMessage();
            return $response;
        }

        $userData = array(
            'id'       => $user->getId(),
            'email'    => $user->getEmail(),
            'name'     => $user->getDisplayName(),
            'role'     => $user->getRole(),
            'language' => $user->getLanguage(),
            'locale'   => $user->getLocale(),
            'timezone' => $user->getTimezone(),
            'state'    => $user->getState()
        );

        $data = array(
            'success' => true,
            'data'    => $userData,
        );

        return $data;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $timezone
     * @param string $language
     * @param string $locale
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    private function validateParams(
        $email,
        $password,
        $timezone,
        $language,
        $locale
    ) {
        if (empty($password)) {
            throw new \InvalidArgumentException('Password cannot be empty');
        }

        $validEmail = $this->getEmailValidator()->isValid($email);
        if (!$validEmail) {
            throw new \InvalidArgumentException('Email is not valid');
        }

        $validLocale = $this->getLocale()->setLocale($locale)->isValidLocale();
        if (!$validLocale) {
            throw new \InvalidArgumentException('Locale is not valid');
        }

        $validLanguage = $this->getLocale()->setLocale($language)->isValidLocale();
        if (!$validLanguage) {
            throw new \InvalidArgumentException('Language is not valid');
        }

        $validTimezone = $this->getLocale()->setTimezone($timezone)->isValidTimezone();
        if (!$validTimezone) {
            throw new \InvalidArgumentException('Timezone is not valid');
        }

        return $this;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $timezone
     * @param string $language
     * @param string $locale
     * @param string $displayName
     *
     * @return User
     * @throws \InvalidArgumentException
     */
    private function getUser(
        $email,
        $password,
        $timezone,
        $language,
        $locale,
        $displayName
    ) {
        $encryptedPassword = $this->getCrypt()->create($password);

        $user = new User();
        $user->setPassword($encryptedPassword);
        $user->setEmail($email);
        $user->setTimezone($timezone);
        $user->setLanguage($language);
        $user->setLocale($locale);
        $user->setDisplayName($displayName);
        $user->setLoginAttempts(0);

        return $user;
    }
}

<?php

namespace Api\Controller\User;

use Api\Entities\User;
use Api\Service\AccessorTrait;
use Api\Service\User\Save as UserSave;
use Egulias\EmailValidator\EmailValidator;
use Api\Service\Locale;

/**
 * Class UpdateController
 *
 * @package Api\Controller\User
 *
 * @method UpdateController setUser(UserSave $userSave)
 * @method UpdateController setEmailValidator(EmailValidator $emailValidator)
 * @method UpdateController setLocale(Locale $locale)
 * @method UserSave         getUser()
 * @method EmailValidator   getEmailValidator()
 * @method Locale           getLocale()
 */
class UpdateController
{
    use AccessorTrait;

    /**
     * @param User $user
     *
     * @return array
     */
    public function getResponse(
        User $user,
        $email,
        $name,
        $locale,
        $language,
        $timezone
    ) {
        $data = array(
            'success' => true,
        );

        try {
            $user = $this->setUserData(
                $user,
                $email,
                $name,
                $locale,
                $language,
                $timezone
            );

            $this->getUser()->saveUser($user);
        } catch (\Exception $exc) {
            $data['success'] = false;
            $data['message'] = $exc->getMessage();
        }

        return $data;
    }

    /**
     * @param User   $user
     * @param string $email
     * @param string $name
     * @param string $locale
     * @param string $language
     * @param string $timezone
     *
     * @return User
     * @throws \InvalidArgumentException
     */
    private function setUserData(
        User $user,
        $email,
        $name,
        $locale,
        $language,
        $timezone
    ) {
        $changed = false;

        if (!empty($email)) {
            $valid = $this->getEmailValidator()->isValid($email);
            if (!$valid) {
                throw new \InvalidArgumentException('Email is not valid');
            }

            $user->setEmail($email);
            $changed = true;
        }

        if (!empty($name)) {
            $user->setDisplayName($name);
            $changed = true;
        }

        if (!empty($locale)) {
            if (!$this->getLocale()->setLocale($locale)->isValidLocale()) {
                throw new \InvalidArgumentException('Locale is not valid');
            }

            $user->setLocale($locale);
            $changed = true;
        }

        if (!empty($language)) {
            if (!$this->getLocale()->setLocale($language)->isValidLocale()) {
                throw new \InvalidArgumentException('Language is not valid');
            }

            $user->setLanguage($language);
            $changed = true;
        }

        if (!empty($timezone)) {
            if (!$this->getLocale()->setTimezone($timezone)->isValidTimezone()) {
                throw new \InvalidArgumentException('Timezone is not valid');
            }

            $user->setTimezone($timezone);
            $changed = true;
        }

        if (!$changed) {
            throw new \InvalidArgumentException('Nothing to update');
        }

        return $user;
    }
}

<?php

namespace Api\Controller\User;

use Api\Entities\User;
use Api\Service\Authorization\Token;
use Api\Service\Authorization\Crypt as ModuleCrypt;
use Api\Service\User\Data as UserData;
use Api\Service\User\Save as UserSave;
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
 * @method ModuleCrypt        getCrypt()
 * @method UserData           getUserData()
 * @method UserSave           getUserSave()
 * @method Token              getToken()
 * @method EmailValidator     getEmailValidator()
 */
class RegisterController
{
    use AccessorTrait;

    /**
     * @param User $user
     *
     * @return array
     */
    public function getResponse($email, $password)
    {
        $user = $this->getUserData()->findUser($email);
        if ($user) {
            throw new \RuntimeException('Failed to register new user');
        }

        $response = array('success' => false);

        try {
            $user = $this->getUser($email, $password);
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
     *
     * @return User
     * @throws \InvalidArgumentException
     */
    private function getUser($email, $password)
    {
        $user = new User();

        $valid = $this->getEmailValidator()->isValid($email);
        if (!$valid) {
            throw new \InvalidArgumentException('Email is not valid');
        }

        $user->setEmail($email);

        $encryptedPassword = $this->getCrypt()->create($password);

        $user->setPassword($encryptedPassword);

        $user->setTimezone('Europe/Berlin');
        $user->setDisplayName('User');
        $user->setLanguage('en_US');
        $user->setLocale('Europe/Berlin');

        return $user;
    }
}
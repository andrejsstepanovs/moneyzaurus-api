<?php

namespace Api\Controller\Authenticate;

use Api\Service\Authorization\Token;
use Api\Service\Authorization\Crypt as ModuleCrypt;
use Api\Service\User\Data as UserData;
use Api\Service\User\Save as UserSave;
use Api\Service\AccessorTrait;
use Api\Service\Locale;
use Api\Entities\User;

/**
 * Authentication
 *
 * Class LoginController
 *
 * @package Api\Controller\Authenticate
 *
 * @method LoginController setCrypt(ModuleCrypt $crypt)
 * @method LoginController setUserData(UserData $userData)
 * @method LoginController setUserSave(UserSave $userData)
 * @method LoginController setToken(Token $token)
 * @method LoginController setMaxLoginAttempts(int)
 * @method LoginController setLoginAbuseSleepTime(int)
 * @method LoginController setLocale(Locale $locale)
 * @method ModuleCrypt     getCrypt()
 * @method UserData        getUserData()
 * @method UserSave        getUserSave()
 * @method Locale          getLocale()
 * @method Token           getToken()
 * @method int             getMaxLoginAttempts()
 * @method int             getLoginAbuseSleepTime()
 */
class LoginController
{
    use AccessorTrait;

    /**
     * @param string $username
     * @param string $password
     *
     * @return array
     */
    public function getResponse($username, $password)
    {
        $response = array(
            'success' => false,
        );

        $user = $this->getUserData()->findUser($username);
        if (!$user) {
            sleep(2);

            return $response;
        }

        $loginAttempts = $this->checkLoginAttempts($user);
        $success = $this->getCrypt()->verify($password, $user->getPassword());
        if ($success) {
            $token = $this->getToken()->get($user);
            $loginAttempts = 0;

            $dateTimeFormatter = $this->getLocale()->setUser($user)->getDateTimeFormatter(\IntlDateFormatter::MEDIUM);
            $validUntil = $token->getValidUntil();
            $validUntil->setTimezone(new \DateTimeZone($user->getTimezone()));

            $response['success'] = true;
            $response['data'] = array(
                'id'                => $user->getId(),
                'email'             => $user->getEmail(),
                'token'             => $token->getToken(),
                'expires'           => $dateTimeFormatter->format($validUntil),
                'expires_timestamp' => $validUntil->getTimestamp(),
            );
        }

        $user->setLoginAttempts($loginAttempts);
        $this->getUserSave()->saveUser($user);

        return $response;
    }

    /**
     * @param User $user
     *
     * @return int
     */
    private function checkLoginAttempts(User $user)
    {
        $loginAttempts = $user->getLoginAttempts();
        $loginAttempts++;

        if ($loginAttempts >= $this->getMaxLoginAttempts()) {
            sleep($this->getLoginAbuseSleepTime());
        }

        return $loginAttempts;
    }
}

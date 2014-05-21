<?php

namespace Api\Controller\Authenticate;

use Api\Service\Authorization\Token;
use Api\Service\Authorization\Crypt as ModuleCrypt;
use Api\Service\User\Data as UserData;
use Api\Service\AccessorTrait;

/**
 * Authentication
 *
 * Class LoginController
 *
 * @package Api\Controller\Authenticate
 *
 * @method LoginController setCrypt(ModuleCrypt $crypt)
 * @method LoginController setUserData(UserData $userData)
 * @method LoginController setToken(Token $token)
 * @method ModuleCrypt     getCrypt()
 * @method UserData        getUserData()
 * @method Token           getToken()
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
            'success' => false
        );

        $user = $this->getUserData()->findUser($username);
        if (!$user) {
            sleep(2);
            return $response;
        }

        $success = $this->getCrypt()->verify($password, $user->getPassword());
        if (!$success) {
            return $response;
        }

        $token = $this->getToken()->get($user);

        $response['success'] = true;
        $response['data'] = array(
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'token' => $token->getToken()
        );

        return $response;
    }

}
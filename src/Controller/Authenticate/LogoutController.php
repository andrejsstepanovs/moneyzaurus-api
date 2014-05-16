<?php

namespace Api\Controller\Authenticate;

use Api\Entities\User;
use Api\Service\Authorization\Token;
use Api\Service\AccessorTrait;

/**
 * Class LogoutController
 *
 * @package Api\Controller\Authenticate
 *
 * @method LogoutController setToken(Token $token)
 * @method Token            getToken()
 *
 */
class LogoutController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param string $token
     *
     * @return array
     */
    public function getResponse(User $user, $token)
    {
        $success = $this->getToken()->remove($user, $token);

        $response = array(
            'success' => $success
        );

        return $response;
    }

}
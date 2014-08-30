<?php

namespace Api\Controller\User;

use Api\Entities\User;

/**
 * Class DataController
 *
 * @package Api\Controller\User
 */
class DataController
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function getResponse(User $user)
    {
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
}

<?php

namespace Api\Service\Email\Messages;

use Api\Entities\User;
use Api\Service\AccessorTrait;

/**
 * Class PasswordRecovery
 *
 * @package Api\Service\Email\Messages
 *
 * @method PasswordRecovery setUser(User $user)
 * @method PasswordRecovery setPassword($password)
 * @method User             getUser()
 * @method string           getPassword()
 */
class PasswordRecovery extends AbstractMessage
{
    use AccessorTrait;

    /**
     * @return \Swift_Message
     */
    public function getMessage()
    {
        $subject = 'Password recovery';

        $user = $this->getUser();
        $message = parent::getMessage();

        $message->setSubject($subject);
        $message->setBody($this->getBody());
        $message->setTo([$user->getEmail() => $user->getUsername()]);

        return $message;
    }

    /**
     * @return string
     */
    private function getBody()
    {
        $body = '';

        $body .= 'New password: ';
        $body .= $this->getPassword();

        return $body;
    }
}
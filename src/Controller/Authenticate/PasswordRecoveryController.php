<?php

namespace Api\Controller\Authenticate;

use Api\Entities\User;
use Api\Service\Authorization\Crypt as ModuleCrypt;
use Api\Service\User\Data as UserData;
use Api\Service\User\Save as UserSave;
use Api\Service\Email\Messages\PasswordRecovery as Message;
use Api\Service\AccessorTrait;
use \Swift_Mailer as Mailer;

/**
 * Class PasswordRecoveryController
 *
 * @package Api\Controller\Authenticate
 *
 * @method PasswordRecoveryController setMessage(Message $message)
 * @method PasswordRecoveryController setUserData(UserData $userData)
 * @method PasswordRecoveryController setMailer(Mailer $mailer)
 * @method PasswordRecoveryController setUserSave(UserSave $userSave)
 * @method PasswordRecoveryController setCrypt(ModuleCrypt $crypt)
 * @method Message                    getMessage()
 * @method UserData                   getUserData()
 * @method Mailer                     getMailer()
 * @method UserSave                   getUserSave()
 * @method ModuleCrypt                getCrypt()
 */
class PasswordRecoveryController
{
    use AccessorTrait;

    /**
     * @param string $username
     *
     * @return array
     */
    public function getResponse($username)
    {
        $response = array(
            'success' => false
        );

        $user = $this->getUserData()->findUser($username);
        if (!$user) {
            sleep(2);
            return $response;
        }

        try {
            $response['success'] = (bool)$this->process($user);
        } catch (\Exception $exc) {
            $response['message'] = $exc->getMessage();
        }

        return $response;
    }

    /**
     * @param User $user
     */
    private function process(User $user)
    {
        $password = $this->getCrypt()->getRandomPassword();
        $encryptedPassword = $this->getCrypt()->create($password);

        $user->setPassword($encryptedPassword);
        $this->getUserSave()->saveUser($user);

        $mail = $this->getMessage()->setPassword($password)->setUser($user);
        $success = $this->getMailer()->send($mail->getMessage());

        return $success;
    }
}
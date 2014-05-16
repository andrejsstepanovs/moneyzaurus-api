<?php

namespace Api\Controller\Connection;

use Api\Entities\User;
use Api\Entities\Connection;
use Api\Service\Connection\Save as ConnectionSave;
use Api\Service\Connection\Data as ConnectionData;
use Api\Service\AccessorTrait;
use Api\Service\Email\Messages\ConnectionInvitation as Message;
use \Swift_Mailer as Mailer;

/**
 * Class AddController
 *
 * @package Api\Controller\Distinct
 *
 * @method AddController  setConnectionSave(ConnectionSave $connectionSave)
 * @method AddController  setConnectionData(ConnectionData $connectionData)
 * @method AddController  setMessage(Message $message)
 * @method AddController  setMailer(Mailer $mailer)
 * @method AddController  setConnection(Connection $connection)
 * @method ConnectionSave getConnectionSave()
 * @method ConnectionData getConnectionData()
 * @method Message        getMessage()
 * @method Mailer         getMailer()
 * @method Connection     getConnection()
 */
class AddController
{
    use AccessorTrait;

    /**
     * @param User $user
     *
     * @return array
     */
    public function getResponse(User $user, $email)
    {
        $response = array(
            'success' => false
        );

        try {
            $invitedUser = $this->getConnectionData()->getInvitedUser($email);

            $connection = $this->getConnection()
                ->setUser($user)
                ->setParent($invitedUser)
                ->setDateCreated(new \DateTime());

            $connection = $this->getConnectionSave()->save($connection);
            if ($connection->getId()) {
                $message = $this->getMessage()->setConnection($connection)->getMessage();
                $this->getMailer()->send($message);

                $response['id']      = $connection->getId();
                $response['success'] = true;
            }

        } catch (\InvalidArgumentException $exc) {
            $response['message'] = $exc->getMessage();
        }

        return $response;
    }

}
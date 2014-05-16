<?php

namespace Api\Service\Email\Messages;

use Api\Entities\Connection;
use Api\Service\AccessorTrait;

/**
 * Class ConnectionInvitation
 *
 * @package Api\Service\Email\Messages
 *
 * @method ConnectionInvitation setConnection(Connection $connection)
 * @method Connection           getConnection()
 */
class ConnectionInvitation extends AbstractMessage
{
    use AccessorTrait;

    /**
     * @return \Swift_Message
     */
    public function getMessage()
    {
        $subject = 'New connection invitation';

        $message = parent::getMessage();

        $message->setSubject($subject);
        $message->setBody($this->getBody());
        $message->setTo($this->getConnection()->getParent()->getEmail());

        return $message;
    }

    /**
     * @return string
     */
    private function getBody()
    {
        $body = [];

        $body[] = 'New invitation from: ' . $this->getConnection()->getUser()->getEmail();
        $body[] = 'Please come to moneyzaurus.com and accept it.';

        return implode('<br />', $body);
    }
}
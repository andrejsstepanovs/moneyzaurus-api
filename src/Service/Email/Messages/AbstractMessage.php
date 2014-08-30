<?php

namespace Api\Service\Email\Messages;

use \Swift_Message as Message;
use \Api\Service\AccessorTrait;

/**
 * Class PasswordRecovery
 *
 * @package Api\Service\Email\Messages
 *
 * @method AbstractMessage setSender($sender)
 * @method string          getSender()
 */
abstract class AbstractMessage
{
    use AccessorTrait;

    /** @var Message */
    private $message;

    /**
     * @return Message
     */
    public function getMessage()
    {
        if ($this->message === null) {
            $this->message = Message::newInstance();
            $this->message->setSender($this->getSender());
        }

        return $this->message;
    }

}

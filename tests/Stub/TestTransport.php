<?php

namespace Tests\Stub;

/**
 * Class TestTransport
 *
 * @package Tests\Stub
 */
class TestTransport extends \Swift_SmtpTransport
{
    /**
     * @return bool
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * @param \Swift_Mime_Message $message
     * @param null                $failedRecipients
     *
     * @return int
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        return (int)$message->getDescription();
    }
}
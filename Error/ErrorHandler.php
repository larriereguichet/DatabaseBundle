<?php


namespace LAG\DatabaseBundle\Error;

use LAG\DatabaseBundle\Mail\MailerInterface;
use Monolog\Logger;

class ErrorHandler
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(Logger $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function handleError($type, $message)
    {
        $logMessage = '';

        if ($type === 'backup') {
            $logMessage = 'An error has occurred during the database backup process : ';
        }
        $logMessage .= $message;

        $this
            ->logger
            ->error($logMessage);

        $this
            ->mailer
            ->sendBackupErrorMail($message);
    }
}

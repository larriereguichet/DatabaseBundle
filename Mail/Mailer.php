<?php

namespace LAG\DatabaseBundle\Mail;

use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\Container;

class Mailer implements MailerInterface
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var
     */
    private $from;

    /**
     * @var
     */
    private $to;

    /**
     * @var
     */
    private $databaseName;

    public function __construct(Swift_Mailer $mailer, $from, $to, $databaseName)
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->to = $to;
        $this->databaseName = $databaseName;
    }

    public function sendBackupSuccessMail($attachment)
    {
        if (!$this->isMailingEnabled()) {
            return;
        }

        $message = Swift_Message::newInstance();
        $message
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setSubject(Container::camelize($this->databaseName).' databases backup')
            ->attach(Swift_Attachment::fromPath($attachment))
            ->setBody('Hi ! <br/>Here is the database backup for '.$this->databaseName.'.', 'text/html')
        ;
        $this
            ->mailer
            ->send($message);
    }

    public function sendBackupErrorMail($error)
    {
        if (!$this->isMailingEnabled()) {
            return;
        }

        $message = Swift_Message::newInstance();
        $message
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setSubject(Container::camelize($this->databaseName).' databases backup ERROR')
            ->setBody(
                'Hi ! <br/><br/>'.$error,
                'text/html'
            )
        ;
        $this
            ->mailer
            ->send($message);
    }

    public function isMailingEnabled()
    {
        return null !== $this->to;
    }
}

<?php

namespace LAG\DatabaseBundle\Event\Subscriber;

use LAG\DatabaseBundle\Archive\Manager\ArchiveManagerInterface;
use LAG\DatabaseBundle\Event\BackupEvent;
use LAG\DatabaseBundle\Mail\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BackupSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var ArchiveManagerInterface
     */
    private $archiveManager;

    /**
     * @var string
     */
    private $compressionMethod;

    public function __construct(
        MailerInterface $mailer,
        ArchiveManagerInterface $archiveManager,
        $compressionMethod
    ) {
        $this->mailer = $mailer;
        $this->archiveManager = $archiveManager;
        $this->compressionMethod = $compressionMethod;
    }

    public static function getSubscribedEvents()
    {
        return [
            BackupEvent::BACKUP_DONE => 'onBackupDone'
        ];
    }

    public function onBackupDone(BackupEvent $event)
    {
        $attachment = $event->getBackupFilename();

        // if a compression method is set, it means that the backup should be compressed before being mailed
        if ($this->compressionMethod) {
            $attachment = $this
                ->archiveManager
                ->archive($event->getBackupFilename());
        }

        // send backup mail
        $this
            ->mailer
            ->sendBackupSuccessMail($attachment);
    }
}

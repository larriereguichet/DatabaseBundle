<?php

namespace LAG\DatabaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BackupEvent extends Event
{
    const BACKUP_DONE = 'backup_done';

    private $backupFilename;

    private $isSuccessful;

    /**
     * @var string
     */
    private $errorOutput;

    public function __construct($backupFilename, $isSuccessful, $errorOutput = '')
    {
        $this->backupFilename = $backupFilename;
        $this->isSuccessful = $isSuccessful;
        $this->errorOutput = $errorOutput;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->isSuccessful;
    }

    /**
     * @return string
     */
    public function getBackupFilename()
    {
        return $this->backupFilename;
    }

    /**
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->errorOutput;
    }
}

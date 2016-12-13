<?php

namespace LAG\DatabaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ArchiveEvent extends Event
{
    const ARCHIVE_DONE = 'archive_done';

    /**
     * @var
     */
    private $archiveFilename;

    /**
     * @var
     */
    private $isSuccessful;

    /**
     * @var string
     */
    private $errorMessage;

    public function __construct($archiveFilename, $isSuccessful, $errorMessage = '')
    {
        $this->archiveFilename = $archiveFilename;
        $this->isSuccessful = $isSuccessful;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return mixed
     */
    public function getArchiveFilename()
    {
        return $this->archiveFilename;
    }

    /**
     * @return mixed
     */
    public function getIsSuccessful()
    {
        return $this->isSuccessful;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}

<?php


namespace LAG\DatabaseBundle\Archive\Manager;


use Exception;
use LAG\DatabaseBundle\Error\ErrorHandler;
use LAG\DatabaseBundle\Event\ArchiveEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Process\Process;

class ArchiveManager implements ArchiveManagerInterface
{
    /**
     * @var string
     */
    private $compressionMethod;

    /**
     * @var string
     */
    private $backupDirectory;

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(
        ErrorHandler $errorHandler,
        EventDispatcher $eventDispatcher,
        $compressionMethod,
        $backupDirectory,
        $databaseName
    ) {
        $this->compressionMethod = $compressionMethod;
        $this->backupDirectory = $backupDirectory;
        $this->databaseName = $databaseName;
        $this->errorHandler = $errorHandler;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function archive($backupFilename)
    {
        // if no compression method is configured, no compression will be applied
        if (!$this->compressionMethod) {
            return true;
        }

        if ($this->compressionMethod !== '7z') {
            throw new Exception('Only 7z compression method is allowed');
        }
        $archiveFilename = $this->generateArchiveFilename();

        // compress backup file
        $compressCommand = new Process($this->generate7zCommand($backupFilename, $archiveFilename));
        $compressCommand->run();

        if ($compressCommand->isSuccessful()) {
            $this
                ->errorHandler
                ->handleError('compression', $compressCommand->getErrorOutput());
        } else {
            $this
                ->eventDispatcher
                ->dispatch(
                    ArchiveEvent::ARCHIVE_DONE,
                    new ArchiveEvent(
                        $archiveFilename,
                        $compressCommand->isSuccessful(),
                        $compressCommand->getErrorOutput()
                    )
                );
        }

        return $compressCommand->isSuccessful();
    }

    protected function generate7zCommand($backupFilename, $archiveFilename)
    {
        $command = '7z a '.$archiveFilename.' '.$backupFilename;

        return $command;

    }

    protected function generateArchiveFilename()
    {
        // generate archive filename
        $archiveFilename = sprintf(
            '%s%s-%s.7z',
            $this->backupDirectory,
            $this->databaseName,
            date('Y-m-d_h-i-s')
        );

        return $archiveFilename;
    }
}

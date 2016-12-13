<?php

namespace LAG\DatabaseBundle\Backup\Manager;

use LAG\DatabaseBundle\Backup\Configuration\BackupConfiguration;
use LAG\DatabaseBundle\Error\ErrorHandler;
use LAG\DatabaseBundle\Event\BackupEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class BackupManager implements BackupManagerInterface
{
    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @var string
     */
    private $backupDirectory;

    /**
     * @var string
     */
    private $backupFilenamePattern = '%database_name%-%date%.sql';

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * BackupManager constructor.
     *
     * @param ErrorHandler $errorStack
     * @param EventDispatcher $eventDispatcher
     * @param string $backupDirectory
     */
    public function __construct(ErrorHandler $errorStack, EventDispatcher $eventDispatcher,  $backupDirectory)
    {
        $this->errorHandler = $errorStack;
        $this->backupDirectory = $backupDirectory;
        $this->eventDispatcher = $eventDispatcher;

        $fileSystem = new Filesystem();

        // ensure backup folder exists
        if (!$fileSystem->exists($backupDirectory)) {
            $fileSystem->mkdir($backupDirectory);
        }
    }

    /**
     * Backup database using mysqldump command.
     *
     * @param BackupConfiguration $configuration
     *
     * @return bool
     */
    public function backup(BackupConfiguration $configuration)
    {
        // generate mysql dump command
        $backupFilename = $this->generateBackupFilename($configuration->getDatabaseName());
        $command = $this->generateMysqlDumpCommand($configuration, $backupFilename);

        // run command
        $command = new Process($command);
        $command->run();

        if (!$command->isSuccessful()) {
            // if an error has occurred, we have to log and eventually mail it
            $this
                ->errorHandler
                ->handleError('backup', $command->getErrorOutput());
        } else {
            // dispatch backup done event
            $this
                ->eventDispatcher
                ->dispatch(
                    BackupEvent::BACKUP_DONE,
                    new BackupEvent(
                        $backupFilename,
                        $command->isSuccessful(),
                        $command->getErrorOutput()
                    )
                );
        }

        return $backupFilename;
    }

    /**
     * @param BackupConfiguration $configuration
     * @param $backupFilename
     *
     * @return string
     */
    protected function generateMysqlDumpCommand(BackupConfiguration $configuration, $backupFilename)
    {
        // generating "mysqldump -u user -ppassword database > file.sql" command
        $command = sprintf(
            'mysqldump -u %s -p%s %s > %s',
            $configuration->getDatabaseUser(),
            $configuration->getDatabasePassword(),
            $configuration->getDatabaseName(),
            $backupFilename
        );

        return $command;
    }

    /**
     * @param $databaseName
     *
     * @return string
     */
    protected function generateBackupFilename($databaseName)
    {
        $backupFilename = $this->backupFilenamePattern;
        $backupFilename = str_replace('%database_name%', $databaseName, $backupFilename);
        $backupFilename = str_replace('%date%', date('Y-m-d_h-i-s'), $backupFilename);

        return $backupFilename;
    }
}

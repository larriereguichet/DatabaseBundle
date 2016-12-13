<?php

namespace LAG\DatabaseBundle\Command;

use LAG\DatabaseBundle\Mail\Mailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DatabaseDumpCommand extends ContainerAwareCommand
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $currentBackupFilename;

    /**
     * @var Mailer
     */
    protected $mailer;

    protected function configure()
    {
        $this
            ->setName('lag:database:backup')
            ->setDescription('Backup the database, compress it and send it to the administrator')
            ->addOption(
                'email-to',
                'to',
                InputOption::VALUE_OPTIONAL,
                'If set, an email with the backup in attachment will be sent to this email address. If an error'.
                ' occurred, an email containing the error will be sent too'
            )
            ->addOption(
                'email-from',
                'from',
                InputOption::VALUE_OPTIONAL,
                'From address for all the sent mails'
            )
            ->addOption(
                'directory',
                'dir',
                InputOption::VALUE_OPTIONAL,
                'The path (relative or absolute) where backups will be copied (default to backups)'
            )
            ->addOption(
                'zip',
                'zip',
                InputOption::VALUE_OPTIONAL,
                'The compress method. Only "7z" value allowed for now. If no value passed, no compression will be apply'
            )
            ->addOption(
                'name',
                'name',
                InputOption::VALUE_OPTIONAL,
                'Name of the backup. Usually, the name of the server. By default, it is set to the database name'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $backupDirectory = $this->getBackupDirectory();
        $fileSystem = new Filesystem();

        // create backup directory if not exists
        if (!$fileSystem->exists($backupDirectory)) {
            $fileSystem->mkdir($backupDirectory);
        }

        // backup the database
        $success = $this->backupDatabase();

        if ($success) {
            // get the attachment : if the zip method is set, get the archive path, else get the backup path and false
            // if an error occurred
            $attachment = $this->compressBackup();

            if (false !== $attachment) {
                $this->sendSuccessMail($attachment);
            }
        }
    }

    protected function backupDatabase()
    {
        $container = $this->getContainer();

        // generating "mysqldump -u user -ppassword database > file.sql" command
        $command = sprintf(
            'mysqldump -u %s -p%s %s > %s',
            $container->getParameter('database_user'),
            $container->getParameter('database_password'),
            $container->getParameter('database_name'),
            $this->generateBackupFilename()
        );
        $this
            ->output
            ->writeln('Start database backup...');

        // run mysqldump command
        $command = new Process($command);
        $command->run();

        if ($command->isSuccessful()) {
            $this
                ->output
                ->writeln('Database backup successful');
        } else {
            // send the error to the configured email
            $this->sendErrorMail($command->getErrorOutput());

            // log the error
            $this->logError($command->getErrorOutput());
        }

        return $command->isSuccessful();
    }

    protected function compressBackup()
    {
        if (!$this->input->hasOption('zip')) {
            return $this->generateBackupFilename();
        }
        if ($this->input->getOption('zip') != '7z') {
            throw new InvalidArgumentException('Invalid value for zip options (only 7z allowed)');
        }
        $this
            ->output
            ->writeln('Start backup compression...');

        $backupDirectory = $this
            ->input
            ->getOption('directory');
        $backupName = $this
            ->input
            ->getOption('name');

        // generate archive filename
        $archiveFilename = sprintf(
            '%s%s-%s.7z',
            $backupDirectory,
            $backupName,
            date('Y-m-d_h-i-s')
        );

        // compress backup file
        $compressCommand = new Process(
            '7z a '.$archiveFilename.' '.$this->generateBackupFilename()
        );
        $compressCommand->run();

        if ($compressCommand->isSuccessful()) {
            $this
                ->output
                ->writeln('Backup compression successful');

            return $archiveFilename;
        } else {
            // send the compressing error to the configured email
            $this->sendErrorMail($compressCommand->getErrorOutput());

            // log the error
            $this->logError($compressCommand->getErrorOutput());
        }

        return $compressCommand->isSuccessful();
    }

    protected function sendErrorMail($error)
    {
        // if to is not set, mails are disabled
        if (!$this->input->hasOption('email-from')) {
            return;
        }
        $from = $this
            ->input
            ->getOption('email-from');
        $to = $this
            ->input
            ->getOption('email-to');

        $this
            ->getContainer()
            ->get('lag.database.mailer')
            ->sendBackupErrorMail($from, $to, $error, $this->getDatabaseName());
    }

    protected function sendSuccessMail($attachment)
    {
        // if to is not set, mails are disabled
        if (!$this->input->hasOption('email-from')) {
            return;
        }
        $from = $this
            ->input
            ->getOption('email-from');
        $to = $this
            ->input
            ->getOption('email-to');

        $this
            ->getContainer()
            ->get('lag.database.mailer')
            ->sendBackupSuccessMail($to, $from, $attachment, $this->getDatabaseName());
    }

    protected function generateBackupFilename()
    {
        // generate only one backup name by backup
        if (null === $this->currentBackupFilename) {
            // generate backup file name : directory / name-date.sql
            $backupFilename = sprintf(
                '%s%s-%s.sql',
                $this->getBackupDirectory(),
                $this->getDatabaseName(),
                date('Y-m-d_h-i-s')
            );
            $this->currentBackupFilename = $backupFilename;
        }

        return $this->currentBackupFilename;
    }

    protected function getBackupDirectory()
    {
        $backupDirectory = $this
            ->input
            ->getOption('directory');

        if (!$backupDirectory) {
            $backupDirectory = $this
                    ->getContainer()
                    ->getParameter('kernel.root_dir').'/../backups';
        }

        return $backupDirectory;
    }

    protected function logError($error)
    {
        $this
            ->getContainer()
            ->get('logger')
            ->error('An error has occurred during database backup. '.$error);
    }

    protected function getDatabaseName()
    {
        $name = $this
            ->input
            ->getOption('name');

        if (!$name) {
            $name = $this
                ->getContainer()
                ->getParameter('database_name');
        }

        return $name;
    }
}

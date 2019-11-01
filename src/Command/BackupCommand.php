<?php

namespace LAG\DatabaseBundle\Command;

use LAG\DatabaseBundle\Helper\BackupHelper;
use BackupManager\Filesystems\Destination;
use BackupManager\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BackupCommand extends Command
{
    protected static $defaultName = 'lag:database:backup';

    /**
     * @var Manager
     */
    private $backupManager;

    /**
     * @var BackupHelper
     */
    private $helper;

    public function __construct(BackupHelper $helper, Manager $backupManager)
    {
        parent::__construct();

        $this->helper = $helper;
        $this->backupManager = $backupManager;
    }

    protected function configure()
    {
        $this->setDescription('Backup configured database using Doctrine connection parameters');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Backup database');

        $filename = $this->helper->generateBackupName();
        $this->backupManager->makeBackup()->run($this->helper->getEnvironment(), [
            new Destination('local', $filename),
        ], 'gzip');

        $style->success('The backup file "'.$filename.'" has been successfully created');
    }
}

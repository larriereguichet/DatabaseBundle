<?php

namespace LAG\DatabaseBundle\Command;

use LAG\DatabaseBundle\Helper\BackupHelper;
use BackupManager\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RestoreCommand extends Command
{
    protected static $defaultName = 'lag:database:restore';

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

    public function configure()
    {
        $this->setDescription('Backup configured database using Doctrine connection parameters');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Backup database');

        $backup = $this->helper->findLastBackup();

        if (null === $backup) {
            $style->error('No backup file found in the local backup directory');

            return;
        }
        $this->backupManager->makeRestore()->run('local', $backup, $this->helper->getEnvironment(), 'gzip');

        $style->success('The backup file "'.$backup.'" has been restored');
    }
}

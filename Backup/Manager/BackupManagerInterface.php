<?php

namespace LAG\DatabaseBundle\Backup\Manager;

use LAG\DatabaseBundle\Backup\Configuration\BackupConfiguration;

interface BackupManagerInterface
{
    public function backup(BackupConfiguration $configuration);
}

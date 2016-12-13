<?php

namespace LAG\DatabaseBundle\Tests\Manager;

use Exception;
use LAG\DatabaseBundle\Backup\Configuration\BackupConfiguration;
use LAG\DatabaseBundle\Backup\Manager\BackupManager;
use LAG\DatabaseBundle\Error\ErrorStack;
use LAG\DatabaseBundle\Event\BackupEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class BackupManagerTest extends TestCase
{
    public function testBackup()
    {
        $parameters = $this->getParameters();
        $configuration = new BackupConfiguration(
            $parameters['database_host'],
            $parameters['database_name'],
            $parameters['database_user'],
            $parameters['database_password'],
            $parameters['database_port']
        );
        $errorStack = new ErrorStack();
        $eventDispatcher = new EventDispatcher();
        $isListenerCalled = false;

        $manager = new BackupManager(
            $errorStack,
            $eventDispatcher,
            __DIR__.'/..'
        );
        $eventDispatcher->addListener(BackupEvent::BACKUP_DONE, function (BackupEvent $event) use (&$isListenerCalled) {
            $isListenerCalled = true;
            $this->assertEquals(true, $event->isSuccessful());
        });

        $success = $manager->backup($configuration);

        // command should be successful
        $this->assertEquals(true, $success);
        // errorStack should be empty
        $this->assertCount(0, $errorStack->getStack());
        // listener should be called
        $this->assertEquals(true, $isListenerCalled);
    }

    protected function setUp()
    {
        $parametersFile = __DIR__.'/../parameters.yml';
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($parametersFile)) {
            throw new Exception('You must create '.$parametersFile.' before running BackupManager test');
        }
        $scripts = file_get_contents(__DIR__.'/../Scripts/database_test.sql');

        $parameters = $this->getParameters();
        $command = sprintf(
            'mysql -u %s -p%s -e "%s"',
            $parameters['database_user'],
            $parameters['database_password'],
            $scripts
        );
        exec($command);
    }

    protected function tearDown()
    {
        $parameters = $this->getParameters();

        $command = sprintf(
            'mysql -u %s -p%s -e "%s"',
            $parameters['database_user'],
            $parameters['database_password'],
            'DROP DATABASE '.$parameters['database_name']
        );
        exec($command);
    }

    protected function getParameters()
    {
        $parametersFile = __DIR__.'/../parameters.yml';
        $parameters = Yaml::parse(file_get_contents($parametersFile))['parameters'];

        return $parameters;
    }
}

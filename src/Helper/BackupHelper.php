<?php

namespace LAG\DatabaseBundle\Helper;

use BackupManager\Config\Config;
use DateTime;
use Symfony\Component\Finder\Finder;

class BackupHelper
{
    /**
     * @var string
     */
    private $filenamePattern;

    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var Config
     */
    private $configurationStorage;

    /**
     * @var string
     */
    private $searchPattern;

    public function __construct(
        string $environment,
        string $filenamePattern,
        string $searchPattern,
        string $dateFormat,
        Config $configurationStorage
    ) {
        $this->environment = $environment;
        $this->filenamePattern = $filenamePattern;
        $this->searchPattern = $searchPattern;
        $this->dateFormat = $dateFormat;
        $this->configurationStorage = $configurationStorage;
    }

    public function getLocalRoot(): string
    {
        return $this->configurationStorage->get('local')['root'];
    }

    public function generateBackupName(): string
    {
        $filename = str_replace('{environment}', $this->environment, $this->filenamePattern);
        $filename = str_replace('{date}', (new DateTime())->format($this->dateFormat), $filename);

        return $filename;
    }

    public function findLastBackup(): ?string
    {
        $config = $this->configurationStorage->get('local');

        $finder = new Finder();
        $finder
            ->files()
            ->in($config['root'])
            ->name('backup_'.$this->environment.'*')
            ->sortByName()
            ->reverseSorting()
        ;
        $last = null;

        foreach ($finder as $fileInfo) {
            $last = $fileInfo;
            break;
        }

        return $last;
    }

    /**
     * @return string
     */
    public function getFilenamePattern(): string
    {
        return $this->filenamePattern;
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return Config
     */
    public function getConfigurationStorage(): Config
    {
        return $this->configurationStorage;
    }
}

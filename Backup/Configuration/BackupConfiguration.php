<?php

namespace LAG\DatabaseBundle\Backup\Configuration;

class BackupConfiguration
{
    /**
     * @var string
     */
    private $databaseHost;

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var string
     */
    private $databaseUser;

    /**
     * @var string
     */
    private $databasePassword;

    /**
     * @var string
     */
    private $databasePort;

    /**
     * BackupConfiguration constructor.
     * @param string $databaseHost
     * @param string $databaseName
     * @param string $databaseUser
     * @param string $databasePassword
     * @param string $databasePort
     */
    public function __construct(
        $databaseHost,
        $databaseName,
        $databaseUser,
        $databasePassword,
        $databasePort
    ) {
        $this->databaseHost = $databaseHost;
        $this->databaseName = $databaseName;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
        $this->databasePort = $databasePort;
    }

    /**
     * @return string
     */
    public function getDatabaseHost()
    {
        return $this->databaseHost;
    }

    /**
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getDatabaseUser()
    {
        return $this->databaseUser;
    }

    /**
     * @return string
     */
    public function getDatabasePassword()
    {
        return $this->databasePassword;
    }

    /**
     * @return string
     */
    public function getDatabasePort()
    {
        return $this->databasePort;
    }
}

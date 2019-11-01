<?php

namespace LAG\DatabaseBundle\Tests\Functional;

use LAG\DatabaseBundle\Tests\Functional\Kernel\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return TestKernel::class;
    }

    public function testWiring(): void
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->assertEquals('mysql:username:password@host/database', $container->getParameter('lag.database.mysql.dsn'));
        $this->assertEquals('Ymd_his', $container->getParameter('lag.database.date_format'));
        $this->assertEquals('backup_{environment}_', $container->getParameter('lag.database.search_pattern'));
        $this->assertEquals('backup_{environment}_{date}.sql', $container->getParameter('lag.database.filename_pattern'));
    }
}

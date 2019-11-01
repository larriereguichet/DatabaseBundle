<?php

namespace LAG\DatabaseBundle\Tests\Functional\Kernel;

use LAG\DatabaseBundle\LAGDatabaseBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new LAGDatabaseBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}

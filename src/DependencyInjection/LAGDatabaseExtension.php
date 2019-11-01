<?php

namespace LAG\DatabaseBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class LAGDatabaseExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator([
            __DIR__.'/../Resources/config',
        ]));
        $loader->load('services.yaml');

        if ('test' === $container->getParameter('kernel.environment')) {
            $loader->load('services_test.yaml');
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lag.database.filename_pattern', $config['filename_pattern']);
        $container->setParameter('lag.database.search_pattern', $config['search_pattern']);
        $container->setParameter('lag.database.date_format', $config['date_format']);
    }
}

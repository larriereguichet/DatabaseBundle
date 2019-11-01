<?php

namespace LAG\DatabaseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('lag_database');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('filename_pattern')->defaultValue('backup_{environment}_{date}.sql')->end()
                    ->scalarNode('search_pattern')->defaultValue('backup_{environment}_')->end()
                    ->scalarNode('date_format')->defaultValue('Ymd_his')->end()
                ->end()
        ->end();

        return $treeBuilder;
    }
}

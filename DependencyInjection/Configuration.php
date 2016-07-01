<?php

namespace Dugun\QueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dugun_queue');

        $rootNode
            ->children()
                ->arrayNode('queues')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('id')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->enumNode('provider')
                                ->isRequired()
                                ->values(['google_pubsub', 'sqs'])
                            ->end()
                            ->scalarNode('topic')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

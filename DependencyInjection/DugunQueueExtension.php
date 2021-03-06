<?php

namespace Dugun\QueueBundle\DependencyInjection;

use Dugun\QueueBundle\Queue\AwsSqs\Queue as AwsSqs;
use Dugun\QueueBundle\Queue\GoogleAppEnginePubSub\Queue as GooglePubSub;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class DugunQueueExtension.
 *
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class DugunQueueExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('dugun_queue', $config);

        foreach ($config['queues'] as $name => $queueConfig) {
            switch ($queueConfig['provider']) {
                case 'aws_sqs':
                    $definition = new Definition(
                        AwsSqs::class,
                        [new Reference('aws.sqs'), $queueConfig['url']]
                    );
                    break;
                case 'google_pubsub':
                    $definition = new Definition(
                        GooglePubSub::class,
                        [$queueConfig['id'], $queueConfig['topic'], $queueConfig['subscriber']]
                    );
                    break;
                default:
                    throw new \InvalidArgumentException();
            }

            $definition->addTag('dugun.queue');
            $definition->setPublic(true);
            $container->setDefinition('dugun_queue.'.$name, $definition);
        }
    }
}

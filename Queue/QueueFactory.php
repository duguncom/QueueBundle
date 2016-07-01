<?php

namespace Dugun\QueueBundle\Queue;

use Dugun\QueueBundle\Queue\GoogleAppEngineSubPub\Queue;

/**
 * Class QueueFactory.
 *
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class QueueFactory
{
    public function __construct($config)
    {
        $this->queues = $config['queues'];
    }

    /**
     * @param $name
     *
     * @return QueueInterface
     *
     * @throws \Exception
     */
    public function get($name)
    {
        $queueConfig = $this->getQueueConfig($name);

        switch ($queueConfig['provider']) {
            case 'google_pubsub':
                $queue = new Queue($queueConfig['id']);
                $queue->setQueueId($queueConfig['topic']);

                return $queue;
            default:
                throw new \Exception(sprintf('%s is not implemented yet', $queueConfig['provider']));
        }
    }

    /**
     * @param $name
     *
     * @return array
     */
    private function getQueueConfig($name)
    {
        if (!array_key_exists($name, $this->queues)) {
            throw new \InvalidArgumentException('No such configured queue');
        }

        return $this->queues[$name];
    }
}

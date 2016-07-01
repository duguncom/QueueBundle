<?php

namespace Dugun\QueueBundle\Queue;

/**
 * Class Queue.
 * 
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
abstract class AbstractQueue implements QueueInterface
{
    /**
     * @param $queue
     *
     * @return mixed
     */
    public function getMessage($queue)
    {
        $messages = $this->getMessages($queue, 1);

        if (!count($messages)) {
            return;
        }

        return $messages[0];
    }
}

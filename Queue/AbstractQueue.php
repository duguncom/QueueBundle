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
     * @return mixed
     */
    public function getMessage()
    {
        $messages = $this->getMessages(1);

        if (!count($messages)) {
            return;
        }

        return $messages[0];
    }
}

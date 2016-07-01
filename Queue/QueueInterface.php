<?php

namespace Dugun\QueueBundle\Queue;

/**
 * Class QueueInterface.
 * 
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
interface QueueInterface
{
    public function sendMessage($message);

    public function getMessages($count);

    public function sendMessageToQueue($queue, $message);

    public function getMessagesFromQueue($queue, $count);
}

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

    public function sendMessages(array $messages);

    public function getMessages($count);

    public function sendMessageToQueue($queue, $message);

    public function sendMessagesToQueue($queue, array $messages);

    public function getMessagesFromQueue($queue, $count);
}

<?php

namespace Dugun\QueueBundle\Queue\GoogleAppEngineSubPub;

use Dugun\QueueBundle\Queue\AbstractQueue;
use Dugun\QueueBundle\Queue\Serializer;
use Google_Client;
use Google_Service_Pubsub;
use Google_Service_Pubsub_PublishRequest;
use Google_Service_Pubsub_PubsubMessage;

/**
 * Class Queue.
 *
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class Queue extends AbstractQueue
{
    /**
     * @var string
     */
    private $projectId;

    /**
     * @var Google_Service_Pubsub
     */
    private $pubSub;

    /**
     * @var string
     */
    private $queueId;

    public function __construct($projectId)
    {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Google_Service_Pubsub::PUBSUB);

        $this->pubSub = new Google_Service_Pubsub($client);
        $this->projectId = $projectId;
    }

    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;
    }

    public function sendMessage($message)
    {
        $this->sendMessageToQueue($this->queueId, $message);
    }

    public function sendMessages(array $messages)
    {
        $this->sendMessagesToQueue($this->queueId, $messages);
    }

    public function getMessages($count)
    {
        return $this->getMessagesFromQueue($this->queueId, $count);
    }

    /**
     * @param $queue
     * @param $message
     */
    public function sendMessageToQueue($queue, $message)
    {
        $this->sendMessagesToQueue($queue, [$message]);
    }

    /**
     * @param $queue
     * @param array $messages
     */
    public function sendMessagesToQueue($queue, array $messages)
    {
        $messagesData = [];
        foreach ($messages as $message) {
            $pubSubMessage = new Google_Service_Pubsub_PubsubMessage();
            $pubSubMessage->setData(Serializer::serialize($message));
            $messagesData[] = $pubSubMessage;
        }

        $publishRequest = new Google_Service_Pubsub_PublishRequest();
        $publishRequest->setMessages($messagesData);

        $this->pubSub->projects_topics->publish($this->getTopic($queue), $publishRequest);
    }

    /**
     * @param $queue
     * @param $count
     *
     * @return array
     */
    public function getMessagesFromQueue($queue, $count)
    {
        $pullRequest = new \Google_Service_Pubsub_PullRequest();
        $pullRequest->setMaxMessages($count);
        $receivedMessages = $this->pubSub
            ->projects_subscriptions
            ->pull($this->getSubscription($queue), $pullRequest)
            ->getReceivedMessages();

        $messages = $ackIds = [];
        foreach ($receivedMessages as $receivedMessage) {
            $ackIds[] = $receivedMessage['ackId'];
            $messages[] = Serializer::unserialize($receivedMessage['message']['data']);
        }

        if (count($ackIds)) {
            $acknowledgeRequest = new \Google_Service_Pubsub_AcknowledgeRequest();
            $acknowledgeRequest->setAckIds($ackIds);
            $this->pubSub->projects_subscriptions->acknowledge($this->getSubscription($queue), $acknowledgeRequest);
        }

        return $messages;
    }

    private function getProject()
    {
        return sprintf('projects/%s', $this->projectId);
    }

    private function getTopic($id)
    {
        return sprintf('%s/topics/%s', $this->getProject(), $id);
    }

    private function getSubscription($id)
    {
        return sprintf('%s/subscriptions/%s', $this->getProject(), $id);
    }
}

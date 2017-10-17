<?php

namespace Dugun\QueueBundle\Queue\AwsSqs;

use Aws\Sqs\SqsClient;
use Dugun\QueueBundle\Queue\AbstractQueue;
use Dugun\QueueBundle\Queue\Serializer;

class Queue extends AbstractQueue
{
    /**
     * @var SqsClient
     */
    private $sqsClient;
    /**
     * @var
     */
    private $queueUrl;

    public function __construct(SqsClient $sqsClient, $queueUrl)
    {
        $this->sqsClient = $sqsClient;
        $this->queueUrl = $queueUrl;
    }

    public function sendMessage($message)
    {
        $this->sqsClient->sendMessage([
            'QueueUrl' => $this->queueUrl,
            'MessageBody' => Serializer::serialize($message),
        ]);
    }

    /**
     * @todo use sendMessageBatch
     *
     * @param array $messages
     */
    public function sendMessages(array $messages)
    {
        foreach ($messages as $message) {
            $this->sqsClient->sendMessage([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => Serializer::serialize($message),
            ]);
        }
    }

    /**
     * @param $count
     *
     * @return array
     */
    public function getMessages($count)
    {
        $messages = $this->sqsClient->receiveMessage([
            'QueueUrl' => $this->queueUrl,
            'MaxNumberOfMessages' => $count,
        ]);

        $messages = $messages->get('Messages');

        if (empty($messages)) {
            return [];
        }

        $result = [];
        foreach ($messages as $message) {
            try {
                $body = Serializer::unserialize($message['Body']);
            } catch (\Exception $e) {
                $body = null;
            }

            $result[$message['MessageId']] = [
                'body' => $body,
                'info' => $message,
            ];
        }

        return $result;
    }

    public function acknowledge($id)
    {
        $this->sqsClient->deleteMessage([
            'QueueUrl' => $this->queueUrl,
            'ReceiptHandle' => $id,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Enqueue\Null;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Interop\Queue\TopicInterface;

class NullContext implements ContextInterface
{
    /**
     * @return NullMessage
     */
    public function createMessage(string $body = '', array $properties = [], array $headers = []): MessageInterface
    {
        $message = new NullMessage();
        $message->setBody($body);
        $message->setProperties($properties);
        $message->setHeaders($headers);

        return $message;
    }

    /**
     * @return NullQueue
     */
    public function createQueue(string $name): QueueInterface
    {
        return new NullQueue($name);
    }

    /**
     * @return NullQueue
     */
    public function createTemporaryQueue(): QueueInterface
    {
        return $this->createQueue(uniqid('', true));
    }

    /**
     * @return NullTopic
     */
    public function createTopic(string $name): TopicInterface
    {
        return new NullTopic($name);
    }

    /**
     * @return NullConsumer
     */
    public function createConsumer(DestinationInterface $destination): ConsumerInterface
    {
        return new NullConsumer($destination);
    }

    /**
     * @return NullProducer
     */
    public function createProducer(): ProducerInterface
    {
        return new NullProducer();
    }

    /**
     * @return NullSubscriptionConsumer
     */
    public function createSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        return new NullSubscriptionConsumer();
    }

    public function purgeQueue(QueueInterface $queue): void
    {
    }

    public function close(): void
    {
    }
}

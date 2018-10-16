<?php

declare(strict_types=1);

namespace Enqueue\Pheanstalk;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\PurgeQueueNotSupportedException;
use Interop\Queue\Exception\SubscriptionConsumerNotSupportedException;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Interop\Queue\TopicInterface;
use Pheanstalk\Pheanstalk;

class PheanstalkContext implements ContextInterface
{
    /**
     * @var Pheanstalk
     */
    private $pheanstalk;

    public function __construct(Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * @return PheanstalkMessage
     */
    public function createMessage(string $body = '', array $properties = [], array $headers = []): MessageInterface
    {
        return new PheanstalkMessage($body, $properties, $headers);
    }

    /**
     * @return PheanstalkDestination
     */
    public function createTopic(string $topicName): TopicInterface
    {
        return new PheanstalkDestination($topicName);
    }

    /**
     * @return PheanstalkDestination
     */
    public function createQueue(string $queueName): QueueInterface
    {
        return new PheanstalkDestination($queueName);
    }

    public function createTemporaryQueue(): QueueInterface
    {
        throw TemporaryQueueNotSupportedException::providerDoestNotSupportIt();
    }

    /**
     * @return PheanstalkProducer
     */
    public function createProducer(): ProducerInterface
    {
        return new PheanstalkProducer($this->pheanstalk);
    }

    /**
     * @param PheanstalkDestination $destination
     *
     * @return PheanstalkConsumer
     */
    public function createConsumer(DestinationInterface $destination): ConsumerInterface
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, PheanstalkDestination::class);

        return new PheanstalkConsumer($destination, $this->pheanstalk);
    }

    public function close(): void
    {
        $this->pheanstalk->getConnection()->disconnect();
    }

    public function createSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        throw SubscriptionConsumerNotSupportedException::providerDoestNotSupportIt();
    }

    public function purgeQueue(QueueInterface $queue): void
    {
        throw PurgeQueueNotSupportedException::providerDoestNotSupportIt();
    }

    public function getPheanstalk(): Pheanstalk
    {
        return $this->pheanstalk;
    }
}

<?php

declare(strict_types=1);

namespace Enqueue\Gearman;

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

class GearmanContext implements ContextInterface
{
    /**
     * @var \GearmanClient
     */
    private $client;

    /**
     * @var GearmanConsumer[]
     */
    private $consumers;

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return GearmanMessage
     */
    public function createMessage(string $body = '', array $properties = [], array $headers = []): MessageInterface
    {
        return new GearmanMessage($body, $properties, $headers);
    }

    /**
     * @return GearmanDestination
     */
    public function createTopic(string $topicName): TopicInterface
    {
        return new GearmanDestination($topicName);
    }

    /**
     * @return GearmanDestination
     */
    public function createQueue(string $queueName): QueueInterface
    {
        return new GearmanDestination($queueName);
    }

    public function createTemporaryQueue(): QueueInterface
    {
        throw TemporaryQueueNotSupportedException::providerDoestNotSupportIt();
    }

    /**
     * @return GearmanProducer
     */
    public function createProducer(): ProducerInterface
    {
        return new GearmanProducer($this->getClient());
    }

    /**
     * @param GearmanDestination $destination
     *
     * @return GearmanConsumer
     */
    public function createConsumer(DestinationInterface $destination): ConsumerInterface
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, GearmanDestination::class);

        $this->consumers[] = $consumer = new GearmanConsumer($this, $destination);

        return $consumer;
    }

    public function close(): void
    {
        $this->getClient()->clearCallbacks();

        foreach ($this->consumers as $consumer) {
            $consumer->getWorker()->unregisterAll();
        }
    }

    public function createSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        throw SubscriptionConsumerNotSupportedException::providerDoestNotSupportIt();
    }

    public function purgeQueue(QueueInterface $queue): void
    {
        throw PurgeQueueNotSupportedException::providerDoestNotSupportIt();
    }

    public function getClient(): \GearmanClient
    {
        if (false == $this->client) {
            $this->client = new \GearmanClient();
            $this->client->addServer($this->config['host'], $this->config['port']);
        }

        return $this->client;
    }

    public function createWorker(): \GearmanWorker
    {
        $worker = new \GearmanWorker();
        $worker->addServer($this->config['host'], $this->config['port']);

        return $worker;
    }
}

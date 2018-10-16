<?php

declare(strict_types=1);

namespace Enqueue\Mongodb;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\SubscriptionConsumerNotSupportedException;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Interop\Queue\TopicInterface;
use MongoDB\Client;
use MongoDB\Collection;

class MongodbContext implements ContextInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    public function __construct($client, array $config = [])
    {
        $this->config = array_replace([
            'dbname' => 'enqueue',
            'collection_name' => 'enqueue',
            'polling_interval' => null,
        ], $config);

        $this->client = $client;
    }

    /**
     * @return MongodbMessage
     */
    public function createMessage(string $body = '', array $properties = [], array $headers = []): MessageInterface
    {
        $message = new MongodbMessage();
        $message->setBody($body);
        $message->setProperties($properties);
        $message->setHeaders($headers);

        return $message;
    }

    /**
     * @return MongodbDestination
     */
    public function createTopic(string $name): TopicInterface
    {
        return new MongodbDestination($name);
    }

    /**
     * @return MongodbDestination
     */
    public function createQueue(string $queueName): QueueInterface
    {
        return new MongodbDestination($queueName);
    }

    public function createTemporaryQueue(): QueueInterface
    {
        throw TemporaryQueueNotSupportedException::providerDoestNotSupportIt();
    }

    /**
     * @return MongodbProducer
     */
    public function createProducer(): ProducerInterface
    {
        return new MongodbProducer($this);
    }

    /**
     * @param MongodbDestination $destination
     *
     * @return MongodbConsumer
     */
    public function createConsumer(DestinationInterface $destination): ConsumerInterface
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, MongodbDestination::class);

        $consumer = new MongodbConsumer($this, $destination);

        if (isset($this->config['polling_interval'])) {
            $consumer->setPollingInterval($this->config['polling_interval']);
        }

        return $consumer;
    }

    public function close(): void
    {
    }

    public function createSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        throw SubscriptionConsumerNotSupportedException::providerDoestNotSupportIt();
    }

    /**
     * @param MongodbDestination $queue
     */
    public function purgeQueue(QueueInterface $queue): void
    {
        $this->getCollection()->deleteMany([
            'queue' => $queue->getQueueName(),
        ]);
    }

    public function getCollection(): Collection
    {
        return $this->client
            ->selectDatabase($this->config['dbname'])
            ->selectCollection($this->config['collection_name']);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function createCollection(): void
    {
        $collection = $this->getCollection();
        $collection->createIndex(['priority' => -1, 'published_at' => 1], ['name' => 'enqueue_priority']);
        $collection->createIndex(['delayed_until' => 1], ['name' => 'enqueue_delayed']);
    }
}

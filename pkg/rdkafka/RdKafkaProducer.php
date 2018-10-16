<?php

declare(strict_types=1);

namespace Enqueue\RdKafka;

use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use RdKafka\Producer as VendorProducer;

class RdKafkaProducer implements ProducerInterface
{
    use SerializerAwareTrait;

    /**
     * @var VendorProducer
     */
    private $producer;

    public function __construct(VendorProducer $producer, Serializer $serializer)
    {
        $this->producer = $producer;

        $this->setSerializer($serializer);
    }

    /**
     * @param RdKafkaTopic   $destination
     * @param RdKafkaMessage $message
     */
    public function send(DestinationInterface $destination, MessageInterface $message): void
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, RdKafkaTopic::class);
        InvalidMessageException::assertMessageInstanceOf($message, RdKafkaMessage::class);

        $partition = $message->getPartition() ?: $destination->getPartition() ?: RD_KAFKA_PARTITION_UA;
        $payload = $this->serializer->toString($message);
        $key = $message->getKey() ?: $destination->getKey() ?: null;

        $topic = $this->producer->newTopic($destination->getTopicName(), $destination->getConf());
        $topic->produce($partition, 0 /* must be 0 */, $payload, $key);
    }

    /**
     * @return RdKafkaProducer
     */
    public function setDeliveryDelay(int $deliveryDelay = null): ProducerInterface
    {
        if (null === $deliveryDelay) {
            return $this;
        }

        throw new \LogicException('Not implemented');
    }

    public function getDeliveryDelay(): ?int
    {
        return null;
    }

    /**
     * @return RdKafkaProducer
     */
    public function setPriority(int $priority = null): ProducerInterface
    {
        if (null === $priority) {
            return $this;
        }

        throw new \LogicException('Not implemented');
    }

    public function getPriority(): ?int
    {
        return null;
    }

    public function setTimeToLive(int $timeToLive = null): ProducerInterface
    {
        if (null === $timeToLive) {
            return $this;
        }

        throw new \LogicException('Not implemented');
    }

    public function getTimeToLive(): ?int
    {
        return null;
    }
}

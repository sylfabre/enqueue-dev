<?php

declare(strict_types=1);

namespace Enqueue\Null;

use Interop\Queue\DestinationInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;

class NullProducer implements ProducerInterface
{
    private $priority;

    private $timeToLive;

    private $deliveryDelay;

    public function send(DestinationInterface $destination, MessageInterface $message): void
    {
    }

    /**
     * @return NullProducer
     */
    public function setDeliveryDelay(int $deliveryDelay = null): ProducerInterface
    {
        $this->deliveryDelay = $deliveryDelay;

        return $this;
    }

    public function getDeliveryDelay(): ?int
    {
        return $this->deliveryDelay;
    }

    /**
     * @return NullProducer
     */
    public function setPriority(int $priority = null): ProducerInterface
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @return NullProducer
     */
    public function setTimeToLive(int $timeToLive = null): ProducerInterface
    {
        $this->timeToLive = $timeToLive;

        return $this;
    }

    public function getTimeToLive(): ?int
    {
        return $this->timeToLive;
    }
}

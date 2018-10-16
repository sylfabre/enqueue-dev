<?php

declare(strict_types=1);

namespace Enqueue\Pheanstalk;

use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Pheanstalk\Pheanstalk;

class PheanstalkProducer implements ProducerInterface
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
     * @param PheanstalkDestination $destination
     * @param PheanstalkMessage     $message
     */
    public function send(DestinationInterface $destination, MessageInterface $message): void
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, PheanstalkDestination::class);
        InvalidMessageException::assertMessageInstanceOf($message, PheanstalkMessage::class);

        $rawMessage = json_encode($message);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf(
                'Could not encode value into json. Error %s and message %s',
                json_last_error(),
                json_last_error_msg()
            ));
        }

        $this->pheanstalk->useTube($destination->getName())->put(
            $rawMessage,
            $message->getPriority(),
            $message->getDelay(),
            $message->getTimeToRun()
        );
    }

    /**
     * @return PheanstalkProducer
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
     * @return PheanstalkProducer
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

    /**
     * @return PheanstalkProducer
     */
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

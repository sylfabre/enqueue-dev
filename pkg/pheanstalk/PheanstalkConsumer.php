<?php

declare(strict_types=1);

namespace Enqueue\Pheanstalk;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\MessageInterface;
use Interop\Queue\QueueInterface;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;

class PheanstalkConsumer implements ConsumerInterface
{
    /**
     * @var PheanstalkDestination
     */
    private $destination;

    /**
     * @var Pheanstalk
     */
    private $pheanstalk;

    public function __construct(PheanstalkDestination $destination, Pheanstalk $pheanstalk)
    {
        $this->destination = $destination;
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * @return PheanstalkDestination
     */
    public function getQueue(): QueueInterface
    {
        return $this->destination;
    }

    /**
     * @return PheanstalkMessage
     */
    public function receive(int $timeout = 0): ?MessageInterface
    {
        if (0 === $timeout) {
            while (true) {
                if ($job = $this->pheanstalk->reserveFromTube($this->destination->getName(), 5)) {
                    return $this->convertJobToMessage($job);
                }
            }
        } else {
            if ($job = $this->pheanstalk->reserveFromTube($this->destination->getName(), $timeout / 1000)) {
                return $this->convertJobToMessage($job);
            }
        }

        return null;
    }

    /**
     * @return PheanstalkMessage
     */
    public function receiveNoWait(): ?MessageInterface
    {
        if ($job = $this->pheanstalk->reserveFromTube($this->destination->getName(), 0)) {
            return $this->convertJobToMessage($job);
        }

        return null;
    }

    /**
     * @param PheanstalkMessage $message
     */
    public function acknowledge(MessageInterface $message): void
    {
        InvalidMessageException::assertMessageInstanceOf($message, PheanstalkMessage::class);

        if (false == $message->getJob()) {
            throw new \LogicException('The message could not be acknowledged because it does not have job set.');
        }

        $this->pheanstalk->delete($message->getJob());
    }

    /**
     * @param PheanstalkMessage $message
     */
    public function reject(MessageInterface $message, bool $requeue = false): void
    {
        $this->acknowledge($message);

        if ($requeue) {
            $this->pheanstalk->release($message->getJob(), $message->getPriority(), $message->getDelay());
        }
    }

    private function convertJobToMessage(Job $job): PheanstalkMessage
    {
        $stats = $this->pheanstalk->statsJob($job);

        $message = PheanstalkMessage::jsonUnserialize($job->getData());
        $message->setRedelivered($stats['reserves'] > 1);
        $message->setJob($job);

        return $message;
    }
}

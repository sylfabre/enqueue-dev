<?php

namespace Enqueue\Consumption\Context;

use Interop\Queue\ContextInterface;
use Psr\Log\LoggerInterface;

final class End
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $endTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContextInterface $context, int $startTime, int $endTime, LoggerInterface $logger)
    {
        $this->context = $context;
        $this->logger = $logger;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * In milliseconds.
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * In milliseconds.
     */
    public function getEndTime(): int
    {
        return $this->startTime;
    }
}

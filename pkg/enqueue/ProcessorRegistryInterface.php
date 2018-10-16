<?php

declare(strict_types=1);

namespace Enqueue;

use Interop\Queue\ProcessorInterface;

interface ProcessorRegistryInterface
{
    public function get(string $processorName): ProcessorInterface;
}

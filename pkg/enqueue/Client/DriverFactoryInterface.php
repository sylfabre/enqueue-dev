<?php

namespace Enqueue\Client;

use Interop\Queue\ConnectionFactoryInterface;

interface DriverFactoryInterface
{
    public function create(ConnectionFactoryInterface $factory, string $dsn, array $config): DriverInterface;
}

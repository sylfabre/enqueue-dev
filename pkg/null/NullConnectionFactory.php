<?php

declare(strict_types=1);

namespace Enqueue\Null;

use Interop\Queue\ConnectionFactoryInterface;
use Interop\Queue\ContextInterface;

class NullConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @return NullContext
     */
    public function createContext(): ContextInterface
    {
        return new NullContext();
    }
}

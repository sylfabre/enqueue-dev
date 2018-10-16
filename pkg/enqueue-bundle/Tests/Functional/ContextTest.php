<?php

namespace Enqueue\Bundle\Tests\Functional;

use Interop\Queue\ContextInterface;

/**
 * @group functional
 */
class ContextTest extends WebTestCase
{
    public function testCouldBeGetFromContainerAsService()
    {
        $connection = static::$container->get('test_enqueue.transport.default.context');

        $this->assertInstanceOf(ContextInterface::class, $connection);
    }
}

<?php

namespace Enqueue\Fs\Tests\Functional;

use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\Extension\LimitConsumedMessagesExtension;
use Enqueue\Consumption\Extension\LimitConsumptionTimeExtension;
use Enqueue\Consumption\Extension\ReplyExtension;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Consumption\Result;
use Enqueue\Fs\FsConnectionFactory;
use Enqueue\Fs\FsContext;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;
use Makasim\File\TempFile;

/**
 * @group functional
 */
class FsConsumptionUseCasesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FsContext
     */
    private $fsContext;

    public function setUp()
    {
        $this->fsContext = (new FsConnectionFactory(['path' => sys_get_temp_dir()]))->createContext();

        new TempFile(sys_get_temp_dir().'/fs_test_queue');
    }

    public function tearDown()
    {
        $this->fsContext->close();
    }

    public function testConsumeOneMessageAndExit()
    {
        $queue = $this->fsContext->createQueue('fs_test_queue');

        $message = $this->fsContext->createMessage(__METHOD__);
        $this->fsContext->createProducer()->send($queue, $message);

        $queueConsumer = new QueueConsumer($this->fsContext, new ChainExtension([
            new LimitConsumedMessagesExtension(1),
            new LimitConsumptionTimeExtension(new \DateTime('+3sec')),
        ]));

        $processor = new StubProcessor();
        $queueConsumer->bind($queue, $processor);

        $queueConsumer->consume();

        $this->assertInstanceOf(MessageInterface::class, $processor->lastProcessedMessage);
        $this->assertEquals(__METHOD__, $processor->lastProcessedMessage->getBody());
    }

    public function testConsumeOneMessageAndSendReplyExit()
    {
        $queue = $this->fsContext->createQueue('fs_test_queue');

        $replyQueue = $this->fsContext->createQueue('fs_test_queue_reply');

        $message = $this->fsContext->createMessage(__METHOD__);
        $message->setReplyTo($replyQueue->getQueueName());
        $this->fsContext->createProducer()->send($queue, $message);

        $queueConsumer = new QueueConsumer($this->fsContext, new ChainExtension([
            new LimitConsumedMessagesExtension(2),
            new LimitConsumptionTimeExtension(new \DateTime('+3sec')),
            new ReplyExtension(),
        ]));

        $replyMessage = $this->fsContext->createMessage(__METHOD__.'.reply');

        $processor = new StubProcessor();
        $processor->result = Result::reply($replyMessage);

        $replyProcessor = new StubProcessor();

        $queueConsumer->bind($queue, $processor);
        $queueConsumer->bind($replyQueue, $replyProcessor);
        $queueConsumer->consume();

        $this->assertInstanceOf(MessageInterface::class, $processor->lastProcessedMessage);
        $this->assertEquals(__METHOD__, $processor->lastProcessedMessage->getBody());

        $this->assertInstanceOf(MessageInterface::class, $replyProcessor->lastProcessedMessage);
        $this->assertEquals(__METHOD__.'.reply', $replyProcessor->lastProcessedMessage->getBody());
    }
}

class StubProcessor implements ProcessorInterface
{
    public $result = self::ACK;

    /** @var MessageInterface */
    public $lastProcessedMessage;

    public function process(MessageInterface $message, ContextInterface $context)
    {
        $this->lastProcessedMessage = $message;

        return $this->result;
    }
}

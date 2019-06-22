<?php declare(strict_types=1);

namespace Qlimix\Tests\Queue\Consumer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Qlimix\Process\Exception\ProcessException;
use Qlimix\Process\Output\OutputInterface;
use Qlimix\Process\Runtime\RuntimeControlInterface;
use Qlimix\Queue\Consumer\QueueConsumerProcess;
use Qlimix\Queue\Processor\Exception\ProcessorException;
use Qlimix\Queue\Processor\ProcessorInterface;

final class QueueConsumerProcessTest extends TestCase
{
    /** @var MockObject */
    private $processor;

    /** @var MockObject */
    private $runtimeControl;

    /** @var MockObject */
    private $output;

    /** @var QueueConsumerProcess */
    private $consumer;

    protected function setUp(): void
    {
        $this->processor =  $this->createMock(ProcessorInterface::class);
        $this->runtimeControl =  $this->createMock(RuntimeControlInterface::class);
        $this->output =  $this->createMock(OutputInterface::class);
        $this->consumer =  new QueueConsumerProcess($this->processor);
    }

    /**
     * @test
     */
    public function shouldRunOneLoop(): void
    {
        $this->processor->expects($this->once())
            ->method('process');

        $this->runtimeControl->expects($this->once())
            ->method('tick');

        $this->runtimeControl->expects($this->once())
            ->method('abort')
            ->willReturn(true);

        $this->consumer->run($this->runtimeControl, $this->output);
    }

    /**
     * @test
     */
    public function shouldRunLoop(): void
    {
        $loops = 5;
        $this->processor->expects($this->exactly($loops))
            ->method('process');

        $this->runtimeControl->expects($this->exactly($loops))
            ->method('tick');

        $this->runtimeControl->expects($this->exactly($loops))
            ->method('abort')
            ->willReturnOnConsecutiveCalls(false, false, false, false, true);

        $this->consumer->run($this->runtimeControl, $this->output);
    }

    /**
     * @test
     */
    public function shouldThrowOnProcessException(): void
    {
        $this->processor->expects($this->once())
            ->method('process')
            ->willThrowException(new ProcessorException());

        $this->runtimeControl->expects($this->once())
            ->method('abort')
            ->willReturn(false);

        $this->expectException(ProcessException::class);

        $this->consumer->run($this->runtimeControl, $this->output);
    }

    /**
     * @test
     */
    public function shouldBreakOnException(): void
    {
        $this->processor->expects($this->once())
            ->method('process')
            ->willThrowException(new ProcessorException());

        $this->runtimeControl->expects($this->once())
            ->method('abort')
            ->willReturn(true);

        $this->consumer->run($this->runtimeControl, $this->output);
    }
}

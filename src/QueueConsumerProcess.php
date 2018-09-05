<?php declare(strict_types=1);

namespace Qlimix\Queue\Consumer;

use Qlimix\Process\Exception\ProcessException;
use Qlimix\Process\Output\OutputInterface;
use Qlimix\Process\ProcessInterface;
use Qlimix\Process\Runtime\ControlInterface;
use Qlimix\Queue\Processor\ProcessorInterface;

final class QueueConsumerProcess implements ProcessInterface
{
    /** @var QueueConsumerInterface */
    private $queueConsumer;

    /** @var ProcessorInterface */
    private $processor;

    /**
     * @param QueueConsumerInterface $queueConsumer
     * @param ProcessorInterface $processor
     */
    public function __construct(QueueConsumerInterface $queueConsumer, ProcessorInterface $processor)
    {
        $this->queueConsumer = $queueConsumer;
        $this->processor = $processor;
    }

    /**
     * @inheritDoc
     */
    public function run(ControlInterface $control, OutputInterface $output): void
    {
        while (true) {
            try {
                $messages = $this->queueConsumer->consume();
            } catch (\Throwable $exception) {
                if ($control->abort()) {
                    break;
                }

                throw new ProcessException('Failing consuming', 0, $exception);
            }

            try {
                foreach ($messages as $message) {
                        $this->processor->process($message);
                        $this->queueConsumer->acknowledge($message);

                    $control->tick();

                    if ($control->abort()) {
                        break 2;
                    }
                }
            } catch (\Throwable $exception) {
                if ($control->abort()) {
                    break;
                }

                throw new ProcessException('Failing processing', 0, $exception);
            }

            $control->tick();

            if ($control->abort()) {
                break;
            }
        }
    }
}

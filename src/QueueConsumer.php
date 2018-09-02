<?php declare(strict_types=1);

namespace Qlimix\Queue\Consumer;

use Qlimix\Process\Exception\ProcessException;
use Qlimix\Process\Output\OutputInterface;
use Qlimix\Process\ProcessInterface;
use Qlimix\Queue\Consumer\Runtime\ControlInterface;
use Qlimix\Queue\Processor\ProcessorInterface;

final class QueueConsumer implements ProcessInterface
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
        $control->init();

        while (true) {
            try {
                $messages = $this->queueConsumer->consume();
                foreach ($messages as $message) {
                    $this->processor->process($message);

                    $control->tick();

                    if ($control->abort()) {
                        break 2;
                    }
                }
            } catch (\Throwable $exception) {
                if ($control->abort()) {
                    break;
                }

                throw new ProcessException('Failing consuming', 0, $exception);
            }

            $control->tick();

            if ($control->abort()) {
                break;
            }
        }
    }
}

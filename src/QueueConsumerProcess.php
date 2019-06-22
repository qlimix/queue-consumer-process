<?php declare(strict_types=1);

namespace Qlimix\Queue\Consumer;

use Qlimix\Process\Exception\ProcessException;
use Qlimix\Process\Output\OutputInterface;
use Qlimix\Process\ProcessInterface;
use Qlimix\Process\Runtime\RuntimeControlInterface;
use Qlimix\Queue\Processor\ProcessorInterface;
use Throwable;

final class QueueConsumerProcess implements ProcessInterface
{
    /** @var ProcessorInterface */
    private $processor;

    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @inheritDoc
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function run(RuntimeControlInterface $control, OutputInterface $output): void
    {
        while (true) {
            try {
                $this->processor->process();
            } catch (Throwable $exception) {
                if ($control->abort()) {
                    break;
                }

                throw new ProcessException('Failed processing', 0, $exception);
            }

            $control->tick();

            if ($control->abort()) {
                break;
            }
        }
    }
}

<?php

namespace App\Handlers;

use App\Interfaces\CommandQueueInterface;
use App\Objects\ExceptionHandlerRegistry;
use Throwable;

class CommandQueueHandler
{

    public function __construct(
        private CommandQueueInterface $queue,
        private ExceptionHandlerRegistry $handlerRegistry,
    ) {}

    public function process(): void
    {
        while (!$this->queue->isEmpty()) {
            $command = $this->queue->getCurrentCommand();

            try {
                $command->execute();
            } catch (\Throwable $e) {
                try {
                    $handler = $this->handlerRegistry->define($command, $e);
                    $handler->handle($command, $e, $this->queue);

                } catch (Throwable) {

                }
            }
        }
    }
}

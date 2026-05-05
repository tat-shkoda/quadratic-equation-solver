<?php

namespace App\Handlers;

use App\Commands\RetryCommand;
use App\Interfaces\CommandInterface;
use App\Interfaces\CommandQueueInterface;
use App\Interfaces\ExceptionHandlerInterface;
use Throwable;

class OnceExceptionHandler implements ExceptionHandlerInterface
{

    public function handle(
        CommandInterface $command,
        Throwable $e,
        CommandQueueInterface $queue,
    ): void {
        $queue->add(new RetryCommand($command));
    }
}

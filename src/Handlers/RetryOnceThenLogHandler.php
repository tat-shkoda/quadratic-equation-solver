<?php

namespace App\Handlers;

use App\Commands\LogExceptionCommand;
use App\Commands\RetryCommand;
use App\Interfaces\CommandInterface;
use App\Interfaces\CommandQueueInterface;
use App\Interfaces\ExceptionHandlerInterface;
use Throwable;

class RetryOnceThenLogHandler implements ExceptionHandlerInterface
{

    public function handle(
        CommandInterface $command,
        Throwable $e,
        CommandQueueInterface $queue,
    ): void {
        if (!$command instanceof RetryCommand) {
            $queue->add(new RetryCommand($command));

            return;
        }

        $queue->add(new LogExceptionCommand($command, $e));
    }
}

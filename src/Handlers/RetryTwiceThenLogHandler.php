<?php

namespace App\Handlers;

use App\Commands\LogExceptionCommand;
use App\Commands\RetryCommand;
use App\Commands\RetryTwiceCommand;
use App\Interfaces\CommandInterface;
use App\Interfaces\CommandQueueInterface;
use App\Interfaces\ExceptionHandlerInterface;
use Throwable;

class RetryTwiceThenLogHandler implements ExceptionHandlerInterface
{

    public function handle(
        CommandInterface $command,
        Throwable $e,
        CommandQueueInterface $queue,
    ): void {
        if (!$command instanceof RetryTwiceCommand) {
            $queue->add(new RetryTwiceCommand($command, 2));

            return;
        }

        if (!$command->isCompleted()) {
            $queue->add($command->next());

            return;
        }

        $queue->add(new LogExceptionCommand($command, $e));
    }
}

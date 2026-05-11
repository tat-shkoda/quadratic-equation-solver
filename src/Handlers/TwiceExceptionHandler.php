<?php

namespace App\Handlers;

use App\Commands\LogExceptionCommand;
use App\Interfaces\CommandInterface;
use App\Interfaces\CommandQueueInterface;
use App\Interfaces\ExceptionHandlerInterface;
use Throwable;

class TwiceExceptionHandler implements ExceptionHandlerInterface
{

    public function handle(
        CommandInterface $command,
        Throwable $e,
        CommandQueueInterface $queue,
    ): void {
        $queue->add(new LogExceptionCommand($command, $e));
    }
}

<?php

namespace App\Interfaces;

use Throwable;

interface ExceptionHandlerInterface
{

    public function handle(CommandInterface $command, Throwable $e, CommandQueueInterface $queue): void;
}

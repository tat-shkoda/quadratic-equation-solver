<?php

namespace App\Objects;

use App\Interfaces\CommandInterface;
use App\Interfaces\ExceptionHandlerInterface;
use Throwable;

class ExceptionHandlerRegistry
{

    private array $map = [];

    public function add(
        CommandInterface $command,
        Throwable $exception,
        ExceptionHandlerInterface $handler
    ): void {
        $this->map[get_class($command)][get_class($exception)] = $handler;
    }

    public function define(
        CommandInterface $command,
        Throwable $exception
    ): ExceptionHandlerInterface {
        return $this->map[get_class($command)][get_class($exception)];
    }
}

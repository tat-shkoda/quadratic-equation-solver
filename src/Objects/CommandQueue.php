<?php

namespace App\Objects;

use App\Interfaces\CommandInterface;
use App\Interfaces\CommandQueueInterface;

class CommandQueue implements CommandQueueInterface
{

    private $commands = [];

    public function add(CommandInterface $command): void
    {
        $this->commands[] = $command;
    }

    public function getCurrentCommand(): ?CommandInterface
    {
        return array_shift($this->commands);
    }

    public function isEmpty(): bool
    {
        return empty($this->commands);
    }
}

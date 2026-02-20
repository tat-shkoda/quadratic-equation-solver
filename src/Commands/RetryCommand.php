<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;

class RetryCommand implements CommandInterface
{

    public function __construct(
        private CommandInterface $command,
        private int $maxAttempts = 1,
        private int $attempt = 1,
    ) {}

    public function execute(): void
    {
        $this->command->execute();
    }

    public function isCompleted(): bool
    {
        return $this->attempt >= $this->maxAttempts;
    }

    public function next(): RetryCommand
    {
        return new RetryCommand(
            $this->command,
            $this->maxAttempts,
            $this->attempt + 1,
        );
    }
}
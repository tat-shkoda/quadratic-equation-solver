<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;

class RetryTwiceCommand implements CommandInterface
{

    public function __construct(
        private CommandInterface $command,
        private int $maxAttempts = 2,
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

    public function next(): RetryTwiceCommand
    {
        return new RetryTwiceCommand(
            $this->command,
            $this->maxAttempts,
            $this->attempt + 1,
        );
    }
}

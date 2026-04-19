<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use Throwable;

class LogExceptionCommand implements CommandInterface
{

    public function __construct(
        private CommandInterface $command,
        private Throwable $exeption,
        private string $file = __DIR__ . '/../../exceptions.log',
    ) {}

    public function execute(): void
    {
        file_put_contents(
            $this->file,
            get_class($this->command) . ': ' . $this->exeption->getMessage(),
            FILE_APPEND,
        );
    }
}

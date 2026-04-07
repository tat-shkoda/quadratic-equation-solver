<?php

namespace App\Commands;

use App\Exceptions\CommandException;
use App\Interfaces\CommandInterface;
use Throwable;

class MacroCommand implements CommandInterface
{
    /**
     * @param []CommandInterface
     */
    public function __construct(
        private array $commands,
    ) {}

    public function execute(): void
    {
        foreach ($this->commands as $command) {
            try {
                $command->execute();
            } catch (Throwable) {
                throw new CommandException();
            }
        }
    }
}

<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\MovingObjectInterface;
use App\IoC;

class UpdateSystemHandler
{

    private null|UpdateSystemHandler $next = null;

    public function __construct(
        private MovingObjectInterface $object,
        private CommandInterface $command,
    ) {}

    public function handle(): void
    {
        $this->command->execute();

        $command = IoC::resolve('collision.macrocommand.get', $this->object);
        $command->execute();

        $this->next?->handle();
    }

    public function setNext(UpdateSystemHandler $next): void
    {
        $this->next = $next;
    }
}

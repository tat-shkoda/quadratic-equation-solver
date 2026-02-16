<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;

class MoveStraightThanBurnFuelCommand implements CommandInterface
{

    public function __construct(
        private $object,
    ) {}

    public function execute(): void
    {
        $command = new MacroCommand([
            new CheckFuelCommand($this->object),
            new Move($this->object),
            new BurnFuelCommand($this->object),
        ]);

        $command->execute();
    }
}

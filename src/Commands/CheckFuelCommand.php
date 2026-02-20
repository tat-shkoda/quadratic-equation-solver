<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\FuelableInterface;
use Exception;

class CheckFuelCommand implements CommandInterface
{

    public function __construct(
        private FuelableInterface $object,
    ) {}

    public function execute(): void
    {
        if ($this->object->getFuelLevel() >= $this->object->getFuelConsumption())
            return;

        throw new Exception();
    }
}

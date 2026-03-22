<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\FuelableInterface;

class BurnFuelCommand implements CommandInterface
{

    public function __construct(
        private FuelableInterface $object,
    ) {}

    public function execute(): void
    {
        $this->object->setFuelLevel(
            $this->object->getFuelLevel() - $this->object->getFuelConsumption(),
        );
    }
}

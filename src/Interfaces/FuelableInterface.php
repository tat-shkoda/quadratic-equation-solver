<?php

namespace App\Interfaces;

interface FuelableInterface
{

    public function getFuelLevel(): int;
    public function setFuelLevel(int $level): void;
    public function getFuelConsumption(): int;
}

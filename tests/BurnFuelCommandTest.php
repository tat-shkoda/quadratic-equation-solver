<?php

use App\Commands\BurnFuelCommand;
use App\Interfaces\FuelableInterface;
use PHPUnit\Framework\TestCase;

class BurnFuelCommandTest extends TestCase
{

    public function testBurnFuelCommand(): void
    {
        $object = new class implements FuelableInterface {
            private int $fuelLevel = 1;
            private int $fuelConsumption = 1;

            public function getFuelLevel(): int
            {
                return $this->fuelLevel;
            }

            public function getFuelConsumption(): int
            {
                return $this->fuelConsumption;
            }

            public function setFuelLevel(int $level): void
            {
                $this->fuelLevel = $level;
            }
        };

        $command = new BurnFuelCommand($object);
        $command->execute();

        $this->assertEquals(0, $object->getFuelLevel());
    }
}

<?php

use App\Commands\MoveStraightThanBurnFuelCommand;
use App\Interfaces\FuelableInterface;
use App\Interfaces\MovingObjectInterface;
use App\Interfaces\PositionInterface;
use App\Objects\Point;
use PHPUnit\Framework\TestCase;

class MoveStraightThanBurnFuelCommandTest extends TestCase
{

    public function testCommandSuccess(): void
    {
        $object = new class implements FuelableInterface, MovingObjectInterface {
            public function __construct(
                private int $fuelLevel = 10,
                private int $fuelConsumption = 2,
                private PositionInterface $position = new Point(0, 0),
                private PositionInterface $velocity = new Point(2, 2),
            ) {}

            public function getFuelLevel(): int
            {
                return $this->fuelLevel;
            }

            public function setFuelLevel(int $level): void
            {
                $this->fuelLevel = $level;
            }

            public function getFuelConsumption(): int
            {
                return $this->fuelConsumption;
            }

            public function getPosition(): PositionInterface
            {
                return $this->position;
            }

            public function getVelocity(): PositionInterface
            {
                return $this->velocity;
            }

            public function setPosition(PositionInterface $point): void
            {
                $this->position = $point;
            }
        };

        $command = new MoveStraightThanBurnFuelCommand($object);
        $command->execute();

        $this->assertEquals(8, $object->getFuelLevel());

        $this->assertEquals(2, $object->getVelocity()->getX());
        $this->assertEquals(2, $object->getVelocity()->getY());
    }
}

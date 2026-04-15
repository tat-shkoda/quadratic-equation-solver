<?php

use App\Commands\CheckFuelCommand;
use App\Interfaces\FuelableInterface;
use PHPUnit\Framework\TestCase;

class CheckFuelCommandTest extends TestCase
{

    public function testCheckFuelCommandSuccess(): void
    {
        $object = $this->createMock(FuelableInterface::class);
        $object->method('getFuelLevel')
            ->willReturn(1);
        $object->method('getFuelConsumption')
            ->willReturn(1);

        $command = new CheckFuelCommand($object);

        $this->assertEquals(null, $command->execute());
    }

    public function testCheckFuelCommandThrowException(): void
    {
        $object = $this->createMock(FuelableInterface::class);
        $object->method('getFuelLevel')
            ->willReturn(1);
        $object->method('getFuelConsumption')
            ->willReturn(2);

        $command = new CheckFuelCommand($object);

        $this->expectException(\Throwable::class);
        $command->execute();
    }
}

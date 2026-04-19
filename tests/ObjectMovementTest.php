<?php

namespace App\Tests;

use App\Commands\Move;
use App\Interfaces\MovingObjectInterface;
use App\Objects\Point;
use App\Objects\Spaceship;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

class ObjectMovementTest extends TestCase
{

    public function testMoveObjectSuccess()
    {
        $object = new Spaceship(new Point(12, 5), new Point(-7, 3));

        $command = new Move($object);
        $command->execute();

        $newPosition = $object->getPosition();

        $this->assertEquals($newPosition->getX(), 5);
        $this->assertEquals($newPosition->getY(), 8);
    }

    public function testMoveThrowsExceptionWhenCannotReadPosition()
    {
        $object = $this->createMock(MovingObjectInterface::class);
        $object->method('getPosition')
            ->willThrowException(new Exception());

        $command = new Move($object);

        $this->expectException(\Throwable::class);
        $command->execute();
    }

    public function testMoveThrowsExceptionWhenCannotReadVelocity()
    {
        $object = $this->createMock(MovingObjectInterface::class);
        $object->method('getVelocity')
            ->willThrowException(new Exception());

        $command = new Move($object);

        $this->expectException(\Throwable::class);
        $command->execute();
    }

    public function testMoveThrowsExceptionWhenCannotSetPosition()
    {
        $object = $this->createMock(MovingObjectInterface::class);
        $object->method('setPosition')
            ->willThrowException(new Exception());

        $command = new Move($object);

        $this->expectException(\Throwable::class);
        $command->execute();
    }
}

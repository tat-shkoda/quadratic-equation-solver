<?php

namespace App\Tests;

use App\Commands\Rotate;
use App\Interfaces\RotatableObjectInterface;
use App\Objects\Point;
use App\Objects\Spaceship;
use Exception;
use PHPUnit\Framework\TestCase;

class ObjectRorationTest extends TestCase
{

    public function testRotateObjectSuccess()
    {
        $object = new Spaceship(
            position: new Point(12, 5),
            velocity: new Point(-7, 3),
            angle: 0,
            angleStep: 1,
        );

        $command = new Rotate($object);
        $command->execute();

        $this->assertEquals($object->getAngle(), 1);
    }

    public function testMoveThrowsExceptionWhenCannotReadAngle()
    {
        $object = $this->createMock(RotatableObjectInterface::class);
        $object->method('getAngle')
            ->willThrowException(new Exception());

        $command = new Rotate($object);

        $this->expectException(\Throwable::class);
        $command->execute();
    }

    public function testMoveThrowsExceptionWhenCannotReadAngleStep()
    {
        $object = $this->createMock(RotatableObjectInterface::class);
        $object->method('getAngleStep')
            ->willThrowException(new Exception());

        $command = new Rotate($object);

        $this->expectException(\Throwable::class);
        $command->execute();
    }

    public function testMoveThrowsExceptionWhenCannotSetAngle()
    {
        $object = $this->createMock(RotatableObjectInterface::class);
        $object->method('setAngle')
            ->willThrowException(new Exception());

        $command = new Rotate($object);

        $this->expectException(\Throwable::class);
        $command->execute();
    }
}

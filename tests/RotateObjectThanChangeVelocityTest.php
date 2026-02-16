<?php

use App\Commands\RotateObjectThanChangeVelocity;
use App\Interfaces\PositionInterface;
use App\Interfaces\RotatableObjectInterface;
use App\Interfaces\VelocityChangableInterface;
use App\Objects\Point;
use PHPUnit\Framework\TestCase;

class RotateObjectThanChangeVelocityTest extends TestCase
{

    public function testRotateObjectThanChangeVelocity(): void
    {
        $object = new class implements RotatableObjectInterface, VelocityChangableInterface {

            public function __construct(
                private PositionInterface $velocity = new Point(5, 5),
                private PositionInterface $velocityStep = new Point(-1, -1),
                private int $angle = 0,
                private int $angleStep = 0,
            ) {}

            public function getAngle(): int
            {
                return $this->angle;
            }
            public function getAngleStep(): int
            {
                return $this->angleStep;
            }

            public function setAngle(int $value): void
            {
                $this->angle = $value;
            }

            public function getVelocity(): PositionInterface
            {
                return $this->velocity;
            }

            public function setVelocity(PositionInterface $velocity): void
            {
                $this->velocity = $velocity;
            }

            public function getChangeVelocityStep(): PositionInterface
            {
                return $this->velocityStep;
            }
        };

        $command = new RotateObjectThanChangeVelocity($object);
        $command->execute();

        $this->assertEquals(4, $object->getVelocity()->getX());
        $this->assertEquals(4, $object->getVelocity()->getY());
    }
}

<?php

use App\Commands\ChangeVelocity;
use App\Interfaces\PositionInterface;
use App\Interfaces\VelocityChangableInterface;
use App\Objects\Point;
use PHPUnit\Framework\TestCase;

class ChangeVelocityTest extends TestCase
{

    public function testCommandSuccess(): void
    {
        $object = new class implements VelocityChangableInterface {
            public function __construct(
                private PositionInterface $velocity = new Point(0, 0),
                private PositionInterface $changeVelocityStep = new Point(2, 2),
            ) {}

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
                return $this->changeVelocityStep;
            }
        };

        $command = new ChangeVelocity($object, new Point(2, 2));
        $command->execute();

        $this->assertEquals(2, $object->getVelocity()->getX());
        $this->assertEquals(2, $object->getVelocity()->getY());
    }
}

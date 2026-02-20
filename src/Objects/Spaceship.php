<?php

namespace App\Objects;

use App\Interfaces\MovingObjectInterface;
use App\Interfaces\PositionInterface;
use App\Interfaces\RotatableObjectInterface;

class Spaceship implements MovingObjectInterface, RotatableObjectInterface
{

    public function __construct(
        private PositionInterface $position,
        private PositionInterface $velocity,
        private int $angle = 0,
        private int $angleStep = 0,
    ) {}

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

    public function getAngle(): int
    {
        return $this->angle;
    }

    public function getAngleStep(): int
    {
        return $this->angleStep;
    }

    public function setAngle(int $angle): void
    {
        $this->angle = $angle;
    }
}

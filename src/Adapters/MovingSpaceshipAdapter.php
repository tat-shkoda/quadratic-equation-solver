<?php

namespace App\Adapters;

use App\Interfaces\MovingObjectInterface;
use App\Interfaces\PositionInterface;
use App\Objects\Spaceship;

class MovingSpaceshipAdapter implements MovingObjectInterface
{

    public function __construct(
        private Spaceship $object,
    ) {}

    public function getPosition(): PositionInterface
    {
        return $this->object->getPosition();
    }

    public function getVelocity(): PositionInterface
    {
        return $this->object->getVelocity();
    }

    public function setPosition(PositionInterface $position): void
    {
        $this->object->setPosition($position);
    }
}

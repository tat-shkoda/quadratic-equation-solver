<?php

namespace App\Interfaces;

interface MovingObjectInterface
{

    public function getPosition(): PositionInterface;
    public function getVelocity(): PositionInterface;

    public function setPosition(PositionInterface $point): void;
}

<?php

namespace App\Objects;

use App\Interfaces\PositionInterface;

class Point implements PositionInterface
{

    public function __construct(
        private int $x,
        private int $y,
    ) {}

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function add(PositionInterface $velocity): PositionInterface
    {
        return new Point(
            $this->x + $velocity->getX(),
            $this->y + $velocity->getY(),
        );
    }
}

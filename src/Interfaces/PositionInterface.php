<?php

namespace App\Interfaces;

interface PositionInterface
{

    public function getX(): int;
    public function getY(): int;
    public function add(PositionInterface $position): PositionInterface;
}

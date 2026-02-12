<?php

namespace App\Interfaces;

interface RotatableObjectInterface
{

    public function getAngle(): int;
    public function getAngleStep(): int;
    public function setAngle(int $value);
}

<?php

namespace App\Objects;

use App\Interfaces\AngleInterface;

class Angle implements AngleInterface
{

    public function __construct(
        private int $angle,
    ) {}

    public function get(): int
    {
        return $this->angle;
    }

    public function add(AngleInterface $angle): AngleInterface
    {
        return new Angle(
            $this->angle + $angle->get(),
        );
    }
}

<?php

namespace App\Commands;

use App\Interfaces\RotatableObjectInterface;

class Rotate
{

    public function __construct(
        private RotatableObjectInterface $object,
    ) {}

    public function execute(): void
    {
        $this->object->setAngle(
            $this->object->getAngle() + $this->object->getAngleStep(),
        );
    }
}

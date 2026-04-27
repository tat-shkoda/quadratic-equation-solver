<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\MovingObjectInterface;

class Move implements CommandInterface
{

    public function __construct(
        private MovingObjectInterface $object,
    ) {}

    public function execute(): void
    {
        $position = $this->object->getPosition();
        $newPosition = $position->add($this->object->getVelocity());
        $this->object->setPosition($newPosition);
    }
}

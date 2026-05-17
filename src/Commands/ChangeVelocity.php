<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\PositionInterface;
use App\Interfaces\VelocityChangableInterface;

class ChangeVelocity implements CommandInterface
{

    public function __construct(
        private VelocityChangableInterface $object,
        private PositionInterface $velocity,
    ) {}

    public function execute(): void
    {
        $this->object->setVelocity($this->velocity);
    }
}

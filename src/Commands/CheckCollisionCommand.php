<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\MovingObjectInterface;
use App\IoC;
use Override;

class CheckCollisionCommand implements CommandInterface
{

    public function __construct(
        private MovingObjectInterface $objectA,
        private MovingObjectInterface $objectB,
        private string $neighborhoodKey,
    ) {}

    #[Override]
    public function execute(): void
    {
        if (IoC::resolve('collision.check', $this->neighborhoodKey, $this->objectA, $this->objectB)) {
            throw new \Exception('Found collision');
        }
    }
}

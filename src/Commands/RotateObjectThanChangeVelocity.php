<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\RotatableObjectInterface;
use App\Interfaces\VelocityChangableInterface;

class RotateObjectThanChangeVelocity implements CommandInterface
{

    public function __construct(
        private $object,
    ) {}

    public function execute(): void
    {
        if (!$this->object instanceof RotatableObjectInterface) {
            return;
        }

        $commands = [
            new Rotate($this->object),
        ];

        if ($this->object instanceof VelocityChangableInterface) {
            $velocity = $this->object->getVelocity();

            $commands[] = new ChangeVelocity(
                $this->object,
                $velocity->add($this->object->getChangeVelocityStep()),
            );
        }

        $command = new MacroCommand($commands);
        $command->execute();
    }
}

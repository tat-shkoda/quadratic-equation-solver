<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\Interfaces\MovingObjectInterface;
use App\IoC;

class UpdateSystemCommand implements CommandInterface
{

    public function __construct(
        private MovingObjectInterface $object,
        private string $system,
    ) {}

    public function execute(): void
    {
        $system = IoC::resolve('object.getSystem', $this->object);

        if ($system === $this->system) return;

        IoC::resolve('system.add', $this->system, $this->object);
        IoC::resolve('system.remove', $system, $this->object);

        $commands = [];
        foreach (IoC::resolve('system.getObjects', $this->system) as $object) {
            if (spl_object_id($this->object) === spl_object_id($object)) continue;

            $commands[] = new CheckCollisionCommand($this->object, $object, $this->system);
        }

        IoC::resolve('collision.macrocommand.set', $this->object, new MacroCommand($commands));
    }
}

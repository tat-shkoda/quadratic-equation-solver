<?php

use App\Commands\MacroCommand;
use App\Commands\UpdateSystemCommand;
use App\Commands\UpdateSystemHandler;
use App\Exceptions\CommandException;
use App\Interfaces\MovingObjectInterface;
use App\IoC;
use PHPUnit\Framework\TestCase;

class CheckCollisionsTest extends TestCase
{

    private array $systems = [];
    private array $commands = [];

    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        $systems = &$this->systems;

        IoC::resolve('IoC.Register', 'system.create', function($key) use (&$systems) {
            $systems[$key] = [];
        });

        IoC::resolve('IoC.Register', 'system.add', function ($key, $object) use (&$systems) {
            $systems[$key][] = $object;
        });

        IoC::resolve('IoC.Register', 'system.remove', function ($key, $object) use (&$systems) {
            $systems[$key] = array_filter(
                $systems[$key] ?? [],
                fn($o) => $o !== $object
            );
        });

        IoC::resolve('IoC.Register', 'system.getObjects', function ($key) use (&$systems) {
            return $systems[$key] ?? [];
        });

        $commands = &$this->commands;

        IoC::resolve(
            'IoC.Register', 'collision.macrocommand.set',
            function ($object, MacroCommand $command) use (&$commands) {
                $commands[spl_object_id($object)] = $command;
            }
        );

        IoC::resolve('IoC.Register', 'collision.macrocommand.get', function ($object) use (&$commands) {
            return $commands[spl_object_id($object)];
        });
    }

    public function testWithoutCollisions(): void
    {
        IoC::resolve('IoC.Register', 'object.getSystem', fn($object) => 'system');
        IoC::resolve('IoC.Register', 'collision.check', fn($object) => false);

        $object = $this->createMock(MovingObjectInterface::class);

        $prevSystem = 'system1';
        IoC::resolve('system.add', $prevSystem, $object);
        IoC::resolve('system.add', $prevSystem, $this->createMock(MovingObjectInterface::class));

        $handler1 = new UpdateSystemHandler($object, new UpdateSystemCommand($object, 'system1'));
        $handler2 = new UpdateSystemHandler($object, new UpdateSystemCommand($object, 'system2'));
        $handler1->setNext($handler2);

        $handler1->handle();

        $this->assertTrue(true);
    }

    public function testWithCollisions(): void
    {
        IoC::resolve('IoC.Register', 'object.getSystem', fn($object) => 'system');
        IoC::resolve('IoC.Register', 'collision.check', fn($object) => true);

        $object = $this->createMock(MovingObjectInterface::class);

        $prevSystem = 'system1';
        IoC::resolve('system.add', $prevSystem, $object);
        IoC::resolve('system.add', $prevSystem, $this->createMock(MovingObjectInterface::class));

        $handler1 = new UpdateSystemHandler($object, new UpdateSystemCommand($object, 'system1'));
        $handler2 = new UpdateSystemHandler($object, new UpdateSystemCommand($object, 'system2'));
        $handler1->setNext($handler2);

        $this->expectException(\Exception::class);
        $handler1->handle();
    }
}

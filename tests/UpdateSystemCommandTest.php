<?php

use App\Commands\MacroCommand;
use App\Commands\UpdateSystemCommand;
use App\Interfaces\MovingObjectInterface;
use App\IoC;
use PHPUnit\Framework\TestCase;

class UpdateSystemCommandTest extends TestCase
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
            return $systems[$key];
        });

        $commands = &$this->commands;

        IoC::resolve(
            'IoC.Register', 'collision.macrocommand.set',
            function ($object, MacroCommand $command) use (&$commands) {
                $commands[spl_object_id($object)] = $command;
            }
        );
    }

    public function testThanSystemWasntChanged(): void
    {
        $object = $this->createMock(MovingObjectInterface::class);

        $system = 'system';

        IoC::resolve('system.create', $system);
        IoC::resolve('IoC.Register', 'object.getSystem', fn($object) => $system);

        new UpdateSystemCommand($object, $system)->execute();

        $this->assertEquals(0, count($this->systems[$system]));
    }

    public function testRemoveObjectThanSystemWasChanged(): void
    {
        $object = $this->createMock(MovingObjectInterface::class);

        $prevSystem = 'system1';
        IoC::resolve('system.add', $prevSystem, $object);

        $this->assertEquals(1, count($this->systems[$prevSystem]));

        IoC::resolve('IoC.Register', 'object.getSystem', fn($object) => $prevSystem);

        $newSystem = 'system2';

        new UpdateSystemCommand($object, $newSystem)->execute();

        $this->assertEquals(0, count($this->systems[$prevSystem]));
    }

    public function testAddObjectThanSystemWasChanged(): void
    {
        $object = $this->createMock(MovingObjectInterface::class);

        $prevSystem = 'system1';
        IoC::resolve('system.add', $prevSystem, $object);
        IoC::resolve('IoC.Register', 'object.getSystem', fn($object) => $prevSystem);

        $newSystem = 'system2';

        new UpdateSystemCommand($object, $newSystem)->execute();

        $this->assertEquals(1, count($this->systems[$newSystem]));
    }

    public function testSetMacrocommand(): void
    {
        $object = $this->createMock(MovingObjectInterface::class);

        $prevSystem = 'system1';
        IoC::resolve('system.add', $prevSystem, $object);

        $this->assertEquals(1, count($this->systems[$prevSystem]));

        IoC::resolve('IoC.Register', 'object.getSystem', fn($object) => $prevSystem);

        $newSystem = 'system2';

        new UpdateSystemCommand($object, $newSystem)->execute();

        $this->assertArrayHasKey(spl_object_id($object), $this->commands);
    }
}

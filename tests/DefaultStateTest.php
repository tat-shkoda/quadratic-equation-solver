<?php

use App\Commands\HardStopCommand;
use App\Commands\MoveToCommand;
use App\Interfaces\CommandInterface;
use App\Interfaces\CommandQueueInterface;
use App\Interfaces\StateInterface;
use App\Objects\CommandQueue;
use App\ServerThread;
use App\States\DefaultState;
use App\States\MoveToState;
use PHPUnit\Framework\TestCase;

class DefaultStateTest extends TestCase
{

    public function testHandleSuccess(): void
    {
        $queue = new CommandQueue();
        $queue->add(new HardStopCommand(new ServerThread()));

        $state = new DefaultState($queue);
        $state = $state->handle();

        $this->assertNull($state);
    }

    public function testThanProcessMoveToCommand(): void
    {
        $queue = new CommandQueue();
        $queue->add($this->createMock(MoveToCommand::class));

        $state = new DefaultState($queue);
        $state = $state->handle();

        $this->assertInstanceOf(MoveToState::class, $state);
    }

    public function testHandleCommands(): void
    {
        $queue = new CommandQueue();
        $queue->add($this->createMock(CommandInterface::class));

        $state = new DefaultState($queue);
        $state = $state->handle();

        $this->assertInstanceOf(DefaultState::class, $state);
    }
}

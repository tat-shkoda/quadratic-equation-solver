<?php

use App\Commands\HardStopCommand;
use App\Commands\StartCommand;
use App\Interfaces\CommandInterface;
use App\Interfaces\CommandQueueInterface;
use App\Objects\CommandQueue;
use App\States\DefaultState;
use App\States\MoveToState;
use PHPUnit\Framework\TestCase;

class MoveToStateTest extends TestCase
{

    public function testThanProcessHardStopCommand(): void
    {
        $queue = new CommandQueue();
        $queue->add($this->createMock(HardStopCommand::class));

        $state = new MoveToState($queue, $this->createMock(CommandQueueInterface::class));
        $state = $state->handle();

        $this->assertNull($state);
    }

    public function testThanProcessStartCommand(): void
    {
        $queue = new CommandQueue();
        $queue->add($this->createMock(StartCommand::class));

        $state = new MoveToState($queue, $this->createMock(CommandQueueInterface::class));
        $state = $state->handle();

        $this->assertInstanceOf(DefaultState::class, $state);
    }

    public function testHandleCommands(): void
    {
        $queue = new CommandQueue();
        $queue->add($this->createMock(CommandInterface::class));
        $queue->add($this->createMock(CommandInterface::class));
        $directionQueue = new CommandQueue();

        $state = new MoveToState($queue, $directionQueue);
        $state = $state->handle();

        $this->assertNull($state);

        $counter = 0;

        while (!$directionQueue->isEmpty()) {
            $directionQueue->getCurrentCommand();
            $counter++;
        }

        $this->assertEquals(2, $counter);
    }
}

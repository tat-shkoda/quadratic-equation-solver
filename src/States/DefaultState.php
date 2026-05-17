<?php

namespace App\States;

use App\Commands\HardStopCommand;
use App\Commands\MoveToCommand;
use App\Interfaces\CommandQueueInterface;
use App\Interfaces\StateInterface;
use App\Objects\CommandQueue;

class DefaultState implements StateInterface
{

    public function __construct(private CommandQueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function handle(): null|StateInterface
    {
        $command = $this->queue->getCurrentCommand();
        $command->execute();

        if ($command instanceof HardStopCommand) {
            return null;
        }

        if ($command instanceof MoveToCommand) {
            $directionQueue = new CommandQueue();

            return new MoveToState($this->queue, $directionQueue);
        }

        return $this;
    }
}

<?php

namespace App\States;

use App\Commands\HardStopCommand;
use App\Commands\StartCommand;
use App\Interfaces\CommandQueueInterface;
use App\Interfaces\StateInterface;

class MoveToState implements StateInterface
{

    public function __construct(
        private CommandQueueInterface $queue,
        private CommandQueueInterface $directionQueue,
    ) {}

    public function handle(): null|StateInterface
    {
        while (!$this->queue->isEmpty()) {
            $command = $this->queue->getCurrentCommand();

            if ($command instanceof HardStopCommand) {
                return null;
            }

            if ($command instanceof StartCommand) {
                return new DefaultState($this->directionQueue);
            }

            $this->directionQueue->add($command);
        }

        return null;
    }
}

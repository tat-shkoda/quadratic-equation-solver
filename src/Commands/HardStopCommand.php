<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\ServerThread;

class HardStopCommand implements CommandInterface
{

    public function __construct(
        private ServerThread $thread,
    ) {}

    public function execute(): void
    {
        $this->thread->hardStop();
    }
}

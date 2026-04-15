<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\ServerThread;

class SoftStopCommand implements CommandInterface
{

    public function __construct(
        private ServerThread $thread,
    ) {}

    public function execute(): void
    {
        $this->thread->softStop();
    }
}

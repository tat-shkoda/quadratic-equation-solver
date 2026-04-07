<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\ServerThread;

class StartCommand implements CommandInterface
{

    public function __construct(
        private ServerThread $thread,
    ) {}

    public function execute(): void
    {
        $this->thread->start();
    }
}

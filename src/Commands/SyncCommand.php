<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use parallel\Channel;

class SyncCommand implements CommandInterface
{

    public function __construct(
        private string $channelName,
    ) {}

    public function execute(): void
    {
        $ch = Channel::open($this->channelName);
        $ch->send(true);
    }
}

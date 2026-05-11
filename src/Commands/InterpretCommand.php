<?php

namespace App\Commands;

use App\Interfaces\CommandInterface;
use App\IoC;
use App\Objects\Message;

class InterpretCommand implements CommandInterface
{

    public function __construct(private Message $message) {}

    public function execute(): void
    {
        $queue = IoC::resolve("games.{$this->message->gameId}");
        $queue->add(
            IoC::resolve(
                "commands.{$this->message->operationId}",
                $this->message->objectId,
                $this->message->args
            )
        );
    }
}

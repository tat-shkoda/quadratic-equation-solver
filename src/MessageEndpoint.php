<?php

namespace App;

use App\Commands\InterpretCommand;
use App\Interfaces\MessageEndpointInterface;
use App\Objects\Message;

class MessageEndpoint implements MessageEndpointInterface
{

    public function handle(string $json): void
    {
        $message = Message::fromJson($json);
        $interpretCommand = new InterpretCommand($message);
        $interpretCommand->execute();
    }
}

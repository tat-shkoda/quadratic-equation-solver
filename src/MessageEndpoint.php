<?php

namespace App;

use App\Commands\InterpretCommand;
use App\Objects\Message;

class MessageEndpoint
{

    public function handle(string $json): void
    {
        $message = Message::fromJson($json);
        $interpretCommand = new InterpretCommand($message);
        $interpretCommand->execute();
    }
}

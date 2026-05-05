<?php

namespace App;

use App\Interfaces\MessageEndpointInterface;
use App\IoC;

class MessageEndpointProxy implements MessageEndpointInterface
{

    public function __construct(
        private MessageEndpointInterface $messageEndpoint,
    ) {}

    public function handle(string $json): void
    {
        $data = json_decode($json, true);

        if (!isset($data['token'])) {
            throw new \InvalidArgumentException('Missing token');
        }

        if (!isset($data['game_id'])) {
            throw new \InvalidArgumentException('Invalid token');
        }

        $token = $data['token'];
        unset($data['token']);

        $decoded = IoC::resolve('jwt.decode', $token);

        if ($decoded->game_id != $data['game_id']) {
            throw new \InvalidArgumentException('Invalid token');
        }

        $this->messageEndpoint->handle(json_encode($data));
    }
}

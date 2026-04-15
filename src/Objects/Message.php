<?php

namespace App\Objects;

class Message
{

    private function __construct(
        public readonly int $gameId,
        public readonly int $objectId,
        public readonly int $operationId,
        public readonly array $args,
    ) {}

    public static function fromJson(string $json): Message
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }

        return new Message(
            gameId: $data['game_id'] ?? throw new \InvalidArgumentException('game_id'),
            objectId: $data['object_id'] ?? throw new \InvalidArgumentException('object_id'),
            operationId: $data['operation_id'] ?? throw new \InvalidArgumentException('operation_id'),
            args: $data['args'] ?? [],
        );
    }
}

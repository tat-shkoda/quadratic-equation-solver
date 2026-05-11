<?php

namespace Auth;

use Firebase\JWT\JWT;

class AuthService
{

    private \Memcached $memcached;

    public function __construct()
    {
        $this->memcached = new \Memcached();
        $this->memcached->addServer('memcached', 11211);
    }

    public function registerGame(array $userIds): string
    {
        $gameId = uniqid();

        $this->memcached->set($gameId, $userIds);

        return $gameId;
    }

    public function auth(string $gameId, string $userId): string
    {
        $userIds = $this->memcached->get($gameId);

        if (!$userIds) throw new \Exception('Game not found');

        if (!in_array($userId, $userIds)) throw new \Exception('User not found in game');

        return JWT::encode(['game_id' => $gameId], 'example_key_of_sufficient_length', 'HS256');
    }
}

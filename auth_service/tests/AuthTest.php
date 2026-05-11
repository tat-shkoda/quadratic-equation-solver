<?php

use Auth\AuthService;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{

    public function testRegisterGameSuccess(): void
    {
        $authService = new AuthService();

        $gameId = $authService->registerGame(['1', '2', '3']);
        $this->assertNotEmpty($gameId);
    }

    public function testGetTokensSuccess(): void
    {
        $authService = new AuthService();

        $gameId = $authService->registerGame(['1', '2', '3']);
        $this->assertNotEmpty($gameId);

        $jwt = $authService->auth($gameId, '1');
        $this->assertNotEmpty($jwt);
    }

    public function testThrowExceptionWhenGameNotFound(): void
    {
        $authService = new AuthService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Game not found');
        $authService->auth('123', '1');
    }

    public function testThrowExceptionWhenUserNotRegisteredInGame(): void
    {
        $authService = new AuthService();

        $gameId = $authService->registerGame(['1', '2', '3']);
        $this->assertNotEmpty($gameId);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found in game');
        $authService->auth($gameId, '4');
    }
}

<?php

use App\Interfaces\MessageEndpointInterface;
use App\IoC;
use App\MessageEndpointProxy;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPUnit\Framework\TestCase;

class MessageEndpointProxyTest extends TestCase
{

    public function setUp(): void
    {
        IoC::resolve('IoC.Register', 'jwt.key', fn() => 'example_key_of_sufficient_length')->execute();
        IoC::resolve('IoC.Register', 'jwt.hash', fn() => 'HS256')->execute();

        IoC::resolve('IoC.Register', 'jwt.decode', function ($token) {
            return JWT::decode($token, new Key(IoC::resolve('jwt.key'), IoC::resolve('jwt.hash')));
        })->execute();

        IoC::resolve('IoC.Register', 'jwt.encode', function ($data) {
            return JWT::encode($data, IoC::resolve('jwt.key'), IoC::resolve('jwt.hash'));
        })->execute();
    }

    public function testHandleMessageSuccess(): void
    {
        $messageEndpoint = $this->createMock(MessageEndpointInterface::class);
        $messageEndpointProxy = new MessageEndpointProxy($messageEndpoint);

        $messageEndpointProxy->handle(json_encode([
            'token' => IoC::resolve('jwt.encode', ['game_id' => 1]),
            'game_id' => 1,
        ]));

        $this->assertTrue(true);
    }

    public function testThrowExceptionWhenMissingToken(): void
    {
        $messageEndpoint = $this->createMock(MessageEndpointInterface::class);
        $messageEndpointProxy = new MessageEndpointProxy($messageEndpoint);

        $this->expectException(\InvalidArgumentException::class);
        $messageEndpointProxy->handle(
            json_encode([
                'game_id' => 1,
            ])
        );
    }

    public function testThrowExceptionWhenInvalidToken(): void
    {
        $messageEndpoint = $this->createMock(MessageEndpointInterface::class);
        $messageEndpointProxy = new MessageEndpointProxy($messageEndpoint);

        $this->expectException(\InvalidArgumentException::class);
        $messageEndpointProxy->handle(
            json_encode([
                'token' => IoC::resolve('jwt.encode', ['game_id' => 2]),
                'game_id' => 1,
            ])
        );
    }
}

<?php

use App\Commands\InterpreterCommand;
use App\Exceptions\ScopeAlreadyExistsException;
use App\Expressions\Expression;
use App\InterpreterContext;
use App\IoC;
use PHPUnit\Framework\TestCase;

class InterpreterTest extends TestCase
{

    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        try {
            IoC::resolve('Scopes.New', 'core');
        } catch (ScopeAlreadyExistsException) {

        }

        IoC::resolve('Scopes.Current', 'core');
    }

    public function testOrderProcessingSuccess(): void
    {
        $objectId = 1;

        $player = uniqid();
        $payload = [
            'player_id' => $player,
            'game_id' => 1,
            'object_id' => $objectId,
            'action' => 'StartMove',
            'initialVelocity' => 2,
        ];

        IoC::resolve('IoC.Register', 'expressions.StartMove', function () use (&$done) {
            return new class extends Expression {
                #[Override]
                public function interpret(InterpreterContext $context): void
                {
                    $context->setProperty('success', true);
                }
            };
        });

        IoC::resolve('Scopes.New', "player.{$player}");
        IoC::resolve('Scopes.Current', "player.{$player}");
        IoC::resolve('IoC.Register', "objects.{$objectId}", fn() => true);

        $context = new InterpreterContext();

        foreach ($payload as $key => $value) {
            $context->setProperty($key, $value);
        }

        new InterpreterCommand($context)->execute();

        $this->assertTrue($context->getProperty('success'));
    }

    public function testThrowExceptionThanAccessDenied(): void
    {
        $objectId = 1;

        $player = uniqid();
        $payload = [
            'player_id' => $player,
            'game_id' => 1,
            'object_id' => $objectId,
            'action' => 'StartMove',
            'initialVelocity' => 2,
        ];

        IoC::resolve('Scopes.New', "player.{$player}");

        $context = new InterpreterContext();

        foreach ($payload as $key => $value) {
            $context->setProperty($key, $value);
        }

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Access denied');

        new InterpreterCommand($context)->execute();
    }
}

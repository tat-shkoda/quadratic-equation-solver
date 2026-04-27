<?php

use App\Commands\Move;
use App\Exceptions\UndefinedMethodException;
use App\Exceptions\UndefinedScopeException;
use App\IoC;
use App\Objects\Point;
use App\Objects\Spaceship;
use PHPUnit\Framework\TestCase;

class IoCTest extends TestCase
{

    public function testRegisterSuccess(): void
    {
        $object = new Spaceship(
            new Point(0, 0),
            new Point(2, 3),
        );

        IoC::resolve('IoC.Register', 'move', fn ($item) => new Move($item))
            ->execute();

        IoC::resolve('move', $object)
            ->execute();

        $this->assertEquals(2, $object->getPosition()->getX());
        $this->assertEquals(3, $object->getPosition()->getY());
    }

    public function testScopesSuccess(): void
    {
        $object = new Spaceship(
            new Point(0, 0),
            new Point(2, 3),
        );

        IoC::resolve('Scopes.New', 'new scope')
            ->execute();
        IoC::resolve('Scopes.Current', 'new scope')
            ->execute();

        IoC::resolve('IoC.Register', 'spaceship', fn ($pos, $vel) => new Spaceship($pos, $vel))
            ->execute();

        IoC::resolve('IoC.Register', 'move', fn ($item) => new Move($item))
            ->execute();

        $cmd = IoC::resolve('move', $object);
        $cmd->execute();

        $this->assertInstanceOf(Move::class, $cmd);
    }

    public function testUndefinedMethod(): void
    {
        $object = new Spaceship(
            new Point(0, 0),
            new Point(2, 3),
        );

        IoC::resolve('Scopes.New', 'first')
            ->execute();
        IoC::resolve('Scopes.Current', 'first')
            ->execute();

        IoC::resolve('IoC.Register', 'move', fn ($item) => new Move($item))
            ->execute();

        IoC::resolve('Scopes.New', 'second')
            ->execute();
        IoC::resolve('Scopes.Current', 'second')
            ->execute();

        $this->expectException(UndefinedMethodException::class);

        IoC::resolve('move', $object);
    }

    public function testUndefinedScope(): void
    {
        IoC::resolve('Scopes.New', 'third')
            ->execute();

        $this->expectException(UndefinedScopeException::class);

        IoC::resolve('Scopes.Current', 'four')
            ->execute();
    }
}

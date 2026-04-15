<?php

namespace App;

use App\Exceptions\ScopeAlreadyExistsException;
use App\Exceptions\UndefinedMethodException;
use App\Exceptions\UndefinedScopeException;
use App\Interfaces\CommandInterface;
use App\Interfaces\IocInterface;

class IoC implements IocInterface
{

    private static array $scopes = [];
    private static string $currentScope = '';

    public static function resolve(string $key, ...$args): mixed
    {
        return match ($key) {
            'IoC.Register' => self::register(...$args),
            'Scopes.New' => self::newScope(...$args),
            'Scopes.Current' => self::switchScope(...$args),
            default => self::get($key, ...$args),
        };
    }

    private static function register(string $key, callable $callable): CommandInterface
    {
        $currentScope = self::$currentScope;

        if ($currentScope === '') {
            self::$scopes[$key] = $callable;
        } else {
            self::$scopes[$currentScope][$key] = $callable;
        }

        return self::getEmptyClass();
    }

    private static function get(string $key, ...$args): mixed
    {
        $currentScope = self::$currentScope;

        if ($currentScope === '') {
            if (!isset(self::$scopes[$key])) {
                throw new UndefinedMethodException();
            }

            return (self::$scopes[$key])(...$args);
        }

        if (!isset(self::$scopes[$currentScope][$key])) {
            throw new UndefinedMethodException();
        }

        return (self::$scopes[$currentScope][$key])(...$args);
    }

    private static function newScope(string $key): CommandInterface
    {
        if (isset(self::$scopes[$key])) {
            throw new ScopeAlreadyExistsException();
        }

        self::$scopes[$key] = [];

        return self::getEmptyClass();
    }

    private static function switchScope(string $key): CommandInterface
    {
        if (!isset(self::$scopes[$key])) {
            throw new UndefinedScopeException();
        }

        self::$currentScope = $key;

        return self::getEmptyClass();
    }

    private static function getEmptyClass(): CommandInterface
    {
        return new class implements CommandInterface
        {

            public function execute(): void
            {

            }
        };
    }
}

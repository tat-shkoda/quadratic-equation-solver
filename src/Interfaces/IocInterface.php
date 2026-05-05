<?php

namespace App\Interfaces;

interface IocInterface
{

    public static function resolve(string $action, ...$args): mixed;
}

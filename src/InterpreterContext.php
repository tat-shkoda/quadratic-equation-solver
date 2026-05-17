<?php

namespace App;

use App\Interfaces\ContextInterface;

class InterpreterContext implements ContextInterface
{

    private array $map = [];

    public function getProperty(string $key): mixed
    {
        return $this->map[$key];
    }

    public function setProperty(string $key, mixed $value): void
    {
        $this->map[$key] = $value;
    }
}

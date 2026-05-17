<?php

namespace App\Interfaces;

interface ContextInterface
{

    public function getProperty(string $key): mixed;
    public function setProperty(string $name, mixed $value): void;
}

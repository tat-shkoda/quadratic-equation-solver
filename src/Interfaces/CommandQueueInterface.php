<?php

namespace App\Interfaces;

interface CommandQueueInterface
{

    public function add(CommandInterface $command): void;
    public function getCurrentCommand(): ?CommandInterface;
    public function isEmpty(): bool;
}

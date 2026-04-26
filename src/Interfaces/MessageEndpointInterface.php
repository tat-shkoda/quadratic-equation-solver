<?php

namespace App\Interfaces;

interface MessageEndpointInterface
{
    public function handle(string $json): void;
}

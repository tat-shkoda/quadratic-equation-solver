<?php

namespace App\Interfaces;

interface AngleInterface
{

    public function get(): int;
    public function add(AngleInterface $value): AngleInterface;
}

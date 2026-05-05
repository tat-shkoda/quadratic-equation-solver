<?php

namespace App\Interfaces;

interface StateInterface
{

    public function handle(): null|StateInterface;
}

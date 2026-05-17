<?php

namespace App\Interfaces;

interface VelocityChangableInterface
{

    public function getVelocity(): PositionInterface;
    public function setVelocity(PositionInterface $velocity): void;
    public function getChangeVelocityStep(): PositionInterface;
}

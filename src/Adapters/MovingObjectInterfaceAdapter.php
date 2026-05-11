<?php

namespace App\Adapters;

use App\IoC;

class MovingObjectInterfaceAdapter implements \App\Interfaces\MovingObjectInterface
{

    public function __construct(private $object) {}
    
    public function getPosition(): \App\Interfaces\PositionInterface
    {
        return IoC::resolve('App\Interfaces\MovingObjectInterface:getPosition', $this->object);
    }

    public function getVelocity(): \App\Interfaces\PositionInterface
    {
        return IoC::resolve('App\Interfaces\MovingObjectInterface:getVelocity', $this->object);
    }

    public function setPosition(\App\Interfaces\PositionInterface $point): void
    {
        IoC::resolve('App\Interfaces\MovingObjectInterface:setPosition', $this->object, $point);
    }

}
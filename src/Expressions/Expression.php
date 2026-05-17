<?php

namespace App\Expressions;

use App\InterpreterContext;

abstract class Expression
{

    abstract public function interpret(InterpreterContext $context): void;

}

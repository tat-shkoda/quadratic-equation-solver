<?php

namespace App\Commands;

use App\Exceptions\UndefinedMethodException;
use App\Interfaces\CommandInterface;
use App\Interfaces\ContextInterface;
use App\IoC;
use Override;

class InterpreterCommand implements CommandInterface
{

    public function __construct(
        private ContextInterface $context
    ) {}

    #[Override]
    public function execute(): void
    {
        $objectId = $this->context->getProperty('object_id');
        $playerId = $this->context->getProperty('player_id');

        IoC::resolve('Scopes.Current', "player.{$playerId}")->execute();

        try {
            if (!IoC::resolve("objects.{$objectId}")) {
                throw new \Exception('Access denied');
            }
        } catch (UndefinedMethodException) {
            throw new \Exception('Access denied');
        }

        $action = $this->context->getProperty('action');

        IoC::resolve('Scopes.Current', 'core')->execute();

        $expression = IoC::resolve("expressions.{$action}");
        $expression->interpret($this->context);
    }
}

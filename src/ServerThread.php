<?php

namespace App;

use Composer\Autoload\ClassLoader;
use parallel\Channel;
use parallel\Future;
use parallel\Runtime;
use ReflectionClass;

class ServerThread
{

    private Channel $queue;
    private ?Future $future = null;
    private string $autoload;

    public function __construct()
    {
        $this->queue = new Channel(Channel::Infinite);

        $this->autoload = dirname(
            (new ReflectionClass(ClassLoader::class))->getFileName(), 2
        ) . '/autoload.php';
    }

    public function start(): void
    {
        $runtime = new Runtime($this->autoload);

        $this->future = $runtime->run(function (Channel $queue) {
            while (true) {
                try {
                    $task = $queue->recv();
                } catch (\parallel\Channel\Error\Closed) {
                    break;
                }

                if ($task === null) {
                    break;
                }

                try {
                    $task->execute();
                } catch (\Throwable) {
                }
            }
        }, [$this->queue]);
    }

    public function add(mixed $task): void
    {
        $this->queue->send($task);
    }

    public function hardStop(): void
    {
        $this->queue->close();
        $this->future?->value();
    }

    public function softStop(): void
    {
        $this->queue->send(null);
        $this->future?->value();
    }
}

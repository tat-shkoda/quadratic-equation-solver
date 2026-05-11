<?php

use App\Commands\HardStopCommand;
use App\Commands\SoftStopCommand;
use App\Commands\StartCommand;
use App\Commands\SyncCommand;
use App\ServerThread;
use parallel\Channel;
use parallel\Events;
use PHPUnit\Framework\TestCase;

class MultithreadTest extends TestCase
{

    public function testStartCommand(): void
    {
        $channelName = 'sync_start_' . uniqid();
        $syncCh = Channel::make($channelName, 1);

        $thread = new ServerThread();
        $thread->add(new SyncCommand($channelName));

        (new StartCommand($thread))->execute();

        $events = new Events();
        $events->addChannel($syncCh);
        $events->setTimeout(5_000_000);

        $received = null;
        try {
            $received = $events->poll();
        } catch (\parallel\Events\Error\Timeout) {
        }

        $this->assertNotNull($received);

        (new HardStopCommand($thread))->execute();
        $syncCh->close();
    }

    public function testHardStopCommand(): void
    {
        $thread = new ServerThread();
        (new StartCommand($thread))->execute();

        $start = microtime(true);
        (new HardStopCommand($thread))->execute();

        $this->assertLessThan(1.0, microtime(true) - $start);
    }

    public function testSoftStopCommand(): void
    {
        $channelName = 'sync_soft_' . uniqid();
        $taskCount = 4;
        $resultCh = Channel::make($channelName, $taskCount);

        $thread = new ServerThread();
        for ($i = 0; $i < $taskCount; $i++) {
            $thread->add(new SyncCommand($channelName));
        }

        (new StartCommand($thread))->execute();
        (new SoftStopCommand($thread))->execute();

        $count = 0;
        for ($i = 0; $i < $taskCount; $i++) {
            $resultCh->recv();
            $count++;
        }

        $this->assertEquals($taskCount, $count);

        $resultCh->close();
    }
}

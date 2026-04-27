<?php

use App\Commands\LogExceptionCommand;
use App\Commands\RetryCommand;
use App\Commands\RetryTwiceCommand;
use App\Handlers\LogExceptionHandler;
use App\Handlers\OnceExceptionHandler;
use App\Handlers\RetryOnceThenLogHandler;
use App\Handlers\RetryTwiceThenLogHandler;
use App\Interfaces\CommandInterface;
use App\Objects\CommandQueue;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{

    public function testLogExceptionCommand(): void
    {
        $filepath = __DIR__ . '/test.log';

        unlink($filepath);

        $command = $this->createMock(CommandInterface::class);

        $exception = new Exception('Exception message');
        $command = new LogExceptionCommand($command, $exception, $filepath);
        $command->execute();

        $fileContent = file_get_contents($filepath);
        unlink($filepath);

        $this->assertStringContainsString($exception->getMessage(), $fileContent);
    }

    public function testLogExceptionHandler(): void
    {
        $queue = new CommandQueue();

        $command = $this->createMock(CommandInterface::class);
        $exception = new Exception();

        $handler = new LogExceptionHandler();
        $handler->handle($command, $exception, $queue);

        $this->assertEquals(
            $queue->getCurrentCommand()::class,
            LogExceptionCommand::class,
        );
    }

    public function testRetryCommand(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $command->method('execute')
            ->willReturnCallback(function () use (&$successfull) {
                $successfull = true;
            });

        $retryCommand = new RetryCommand($command);

        $successfull = false;
        $retryCommand->execute();

        $this->assertEquals(true, $successfull);
    }

    public function testOnceExceptionHandler(): void
    {
        $queue = new CommandQueue();
        $command = $this->createMock(CommandInterface::class);

        $exception = new Exception();

        $exceptionHandler = new OnceExceptionHandler();
        $exceptionHandler->handle($command, $exception, $queue);

        $this->assertEquals(false, $queue->isEmpty());

        $this->assertEquals(
            $queue->getCurrentCommand()::class,
            RetryCommand::class,
        );
    }

    public function testRetryOnceThenLogHandler(): void
    {
        $queue = new CommandQueue();

        $exception = new Exception();
        $command = $this->createMock(CommandInterface::class);

        $handler = new RetryOnceThenLogHandler();
        $handler->handle($command, $exception, $queue);

        $retryCommand = $queue->getCurrentCommand();
        $this->assertEquals(RetryCommand::class, $retryCommand::class);
        $this->assertEquals(true, $queue->isEmpty());

        $handler->handle($retryCommand, $exception, $queue);

        $logCommand = $queue->getCurrentCommand();
        $this->assertEquals(LogExceptionCommand::class, $logCommand::class);
        $this->assertEquals(true, $queue->isEmpty());
    }

    public function testRetryTwiceThenLogHandler(): void
    {
        $queue = new CommandQueue();

        $exception = new Exception();
        $command = $this->createMock(CommandInterface::class);

        $handler = new RetryTwiceThenLogHandler();
        $handler->handle($command, $exception, $queue);

        $retryCommand = $queue->getCurrentCommand();
        $this->assertEquals(RetryTwiceCommand::class, $retryCommand::class);
        $this->assertEquals(true, $queue->isEmpty());

        $handler->handle($retryCommand, $exception, $queue);

        $retryTwiceCommand = $queue->getCurrentCommand();
        $this->assertEquals(RetryTwiceCommand::class, $retryTwiceCommand::class);
        $this->assertEquals(true, $queue->isEmpty());

        $handler->handle($retryTwiceCommand, $exception, $queue);

        $logCommand = $queue->getCurrentCommand();
        $this->assertEquals(LogExceptionCommand::class, $logCommand::class);
        $this->assertEquals(true, $queue->isEmpty());
    }
}

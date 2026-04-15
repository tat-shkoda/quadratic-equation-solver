<?php

use App\Commands\InterpretCommand;
use App\Interfaces\CommandInterface;
use App\IoC;
use App\MessageEndpoint;
use App\Objects\CommandQueue;
use App\Objects\Message;
use PHPUnit\Framework\TestCase;

class MessagingSystemTest extends TestCase
{

    public function testCreateMessage(): void
    {
        $message = Message::fromJson(json_encode([
            'game_id' => 1,
            'object_id' => 2,
            'operation_id' => 3,
            'args' => [
                'arg1' => 'value1',
                'arg2' => 'value2',
            ],
        ]));

        $this->assertEquals(1, $message->gameId);
        $this->assertEquals(2, $message->objectId);
        $this->assertEquals(3, $message->operationId);
        $this->assertEquals(['arg1' => 'value1', 'arg2' => 'value2'], $message->args);
    }

    public function testThrowExceptionWhenInvalidJson(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Message::fromJson('invalid json');
    }

    public function testThrowExceptionWhenMissingRequiredFields(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Message::fromJson('{"game_id": 1, "object_id": 1}');
    }

    public function testMessageEndpoint(): void
    {
        $queue = new CommandQueue();

        $gameId = 1;
        $operationId = 1;
        $objectId = 1;

        IoC::resolve('IoC.Register', "games.{$gameId}", fn() => $queue);
        IoC::resolve('IoC.Register', "commands.{$operationId}", function () {
            return new class implements CommandInterface {
                public function execute(): void {}
            };
        });

        $endpoint = new MessageEndpoint();

        $message = [
            'game_id' => $gameId,
            'object_id' => $objectId,
            'operation_id' => $operationId,
            'args' => [],
        ];

        $endpoint->handle(json_encode($message));

        $this->assertFalse($queue->isEmpty());
    }

    public function testInterpretCommand(): void
    {
        // Задача команды InetrpretCommand на основе IoC контейнера создать команду для нужного объекта, которая которая соответствует приказу, содержащемуся в сообщении и постановки этой команды в очередь команд игры.

        // Например, если сообщение указано, что объект с id 548 должен начать двигаться, то результат InterpretCommand заключается можно описать следующим псевдокодом

        // var obj = IoC.Resolve<UObject>("Игровые объекты", "548"); // "548" получено из входящего сообщения
        // IoC.Resolve("Установить начальное значение скорости", obj, 2); // значение 2 получено из args переданного в сообщении
        // var cmd = IoC.Resolve<Command>("Движение по прямой", obj); // Решение, что нужно выполнить движение по прямой получено из сообщения
        //                                                                                                                     // обратите внимание само значение "Движение по прямой" нельзя читать на прямую из сообщения,
        //                                                                                                                     // чтобы избежать инъекции, когда пользователь попытается выполнить действие, которое ему выполнять не позволено
        // IoC.Resolve<Command>("Очередь команд", cmd).Execute();        // Выполняем команду, которая поместит команду cmd в очередь команд игры.

        $gameId = 1;
        $operationId = 1;
        $objectId = 1;
        $args = [
            'arg1' => 1,
            'arg2' => 2,
        ];

        $queue = new CommandQueue();
        $message = [
            'game_id' => $gameId,
            'object_id' => $objectId,
            'operation_id' => $operationId,
            'args' => $args,
        ];

        $receivedObjectId = null;
        $receivedArgs = null;

        IoC::resolve('IoC.Register', "games.{$gameId}", fn() => $queue);
        IoC::resolve('IoC.Register', "commands.{$operationId}", function (int $objectId, array $args) use (&$receivedObjectId, &$receivedArgs) {
            $receivedObjectId = $objectId;
            $receivedArgs = $args;
            return new class implements CommandInterface {
                public function execute(): void {}
            };
        });

        $queue->add(
            new InterpretCommand(
                Message::fromJson(json_encode($message))
            )
        );

        $command = $queue->getCurrentCommand();
        $command->execute();

        $this->assertInstanceOf(CommandInterface::class, $command);
        $this->assertEquals($objectId, $receivedObjectId);
        $this->assertEquals($args, $receivedArgs);
    }

    public function testSendMessageSuccess(): void
    {
        $producer = new class ('kafka') {
            private RdKafka\Producer $producer;
            private RdKafka\Topic $topic;

            public function __construct(
                private string $host,
                private int $port = 9092,
            ) {
                $producerConf = new RdKafka\Conf();
                $producerConf->set('bootstrap.servers', "{$host}:{$port}");

                $this->producer = new RdKafka\Producer($producerConf);
            }

            public function changeTopic(string $topicName): void
            {
               $this->topic = $this->producer->newTopic($topicName);
            }

            public function send(string $message): void
            {
                $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
                $this->producer->flush(10000);
            }
        };

        $topicName = 'commands-' . uniqid();

        $producer->changeTopic($topicName);

        $initialMessage = [
            'game_id' => 1,
            'object_id' => 1,
            'operation_id' => 1,
            'args' => [
                'arg1' => 'value1',
                'arg2' => 'value2',
            ],
        ];

        $producer->send(json_encode($initialMessage));

        $consumer = new class ('kafka', 9092, $topicName) {
            private RdKafka\KafkaConsumer $consumer;

            public function __construct(
                private string $host,
                private int $port = 9092,
                private string $topicName = 'commands',
            ) {
                $conf = new RdKafka\Conf();
                $conf->set('metadata.broker.list', "{$this->host}:{$this->port}");
                $conf->set('group.id', uniqid('test-'));
                $conf->set('auto.offset.reset', 'earliest');
                $this->consumer = new RdKafka\KafkaConsumer($conf);
                $this->consumer->subscribe([$this->topicName]);
            }

            public function getMessage(): null|string
            {
                $msg = $this->consumer->consume(1000);
                switch ($msg->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        return $msg->payload;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        return null;
                    default:
                        throw new \RuntimeException($msg->errstr());
                }
            }
        };

        $messageEndpoint = new MessageEndpoint();

        $counter = 0;
        $retries = 0;
        while ($retries < 10) {
            $message = $consumer->getMessage();
            if ($message === null) {
                $retries++;

                continue;
            }
            $messageEndpoint->handle($message);
            $counter++;

            break;
        }

        $this->assertEquals(1, $counter);
    }
}

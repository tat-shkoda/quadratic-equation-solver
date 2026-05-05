<?php

use App\AdapterGenerator;
use App\Interfaces\MovingObjectInterface;
use App\Interfaces\PositionInterface;
use App\IoC;
use App\Objects\Point;
use PHPUnit\Framework\TestCase;

class AdapterGeneratorTest extends TestCase
{

    public function testGenerateFile(): void
    {
        $interface = MovingObjectInterface::class;

        IoC::resolve(
            'IoC.Register',
            "{$interface}:setPosition",
            function ($object, $value) {
                $object->position = $value;
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "{$interface}:getPosition",
            fn ($object) => $object->position,
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "{$interface}:getVelocity",
            fn ($object) => $object->velocity,
        )->execute();

        IoC::resolve(
            'IoC.Register',
            'Adapter',
            function ($interface, $object) {
                return (new AdapterGenerator())->generate($interface, $object);
            },
        )->execute();


        $object = new class (new Point(0, 0), new Point(0, 2)) {
            public PositionInterface $position;
            public PositionInterface $velocity;

            public function __construct(
                $position,
                $velocity,
            ) {
                $this->position = $position;
                $this->velocity = $velocity;
            }
        };

        IoC::resolve('Adapter', $interface, $object);

        $shortInterfaceName = (new ReflectionClass($interface))->getShortName();
        $adapterClassName = "{$shortInterfaceName}Adapter";

        $filepath = __DIR__ . "/../src/Adapters/{$adapterClassName}.php";

        $this->assertEquals(true, file_exists($filepath));

        $point = new Point(2, 1);
        IoC::resolve("{$interface}:setPosition", $object, $point);

        $this->assertEquals(
            $point, IoC::resolve("{$interface}:getPosition", $object),
        );
    }
}

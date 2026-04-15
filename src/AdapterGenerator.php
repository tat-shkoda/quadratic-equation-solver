<?php

namespace App;

use Generator;
use ReflectionClass;

class AdapterGenerator
{

    public function generate(string $interface, mixed $object): void
    {
        $reflection = new ReflectionClass($interface);
        $interfaceName = $reflection->getShortName();

        $generator = function () use ($reflection, $interface): Generator {
            foreach ($reflection->getMethods() as $method) {
                $paramsStr = '';
                $args = '';

                if (!empty($params = $method->getParameters())) {
                    $paramsStr .= implode(', ', array_map(
                        fn ($param) => '\\' . $param->getType() . ' $' . $param->getName(),
                        $params
                    ));

                    $args .= ', ' . implode(', ', array_map(
                        fn ($param) => '$' . $param->getName(),
                        $params
                    ));
                }

                $returnType = $method->getReturnType();

                $returnOperator = $returnType->getName() === 'void' ? '' : 'return ';
                $returnTypeStr = $returnType->getName() === 'void' ? 'void' : "\\{$returnType}";

                yield <<<PHP

    public function {$method->getShortName()}({$paramsStr}): $returnTypeStr
    {
        {$returnOperator}IoC::resolve('{$interface}:{$method->getShortName()}', \$this->object{$args});
    }
PHP;
            }
        };

        foreach ($generator() as $piece) {
            $methods .= $piece . "\n";
        }

        $adapterClassName = "{$interfaceName}Adapter";

        $code = <<<PHP
<?php

namespace App\Adapters;

use App\IoC;

class {$adapterClassName} implements \\{$interface}
{

    public function __construct(private \$object) {}
    {$methods}
}
PHP;

        $filepath = __DIR__ . "/Adapters/{$adapterClassName}.php";

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        file_put_contents($filepath, $code);
    }
}

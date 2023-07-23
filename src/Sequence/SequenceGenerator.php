<?php

namespace Edykim\LaravelContainerHelper\Sequence;

use Edykim\LaravelContainerHelper\Traits\BuildMethodSignatures;
use Edykim\LaravelContainerHelper\Traits\CreateInstance;

class SequenceGenerator
{
    use BuildMethodSignatures;
    use CreateInstance;

    public static function generate(
        $app,
        string $interfaceName,
        string|callable ...$classNames,
    ) {
        $instance = null;
        $instances = [];

        foreach($classNames as $className) {
            $instances[] = self::createInstance($app, $className);
        }

        $instanceStr = '$instance = new class($app, ...$instances)
      extends ' . SequenceResolver::class . '
      implements ' . $interfaceName . ' {
          ' . self::getMethods($interfaceName) . '
        };';
        eval($instanceStr);
        return $instance;
    }
}

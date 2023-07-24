<?php

namespace Edykim\LaravelContainerHelper\Lazy;

use Edykim\LaravelContainerHelper\Traits\BuildMethodSignatures;

class LazyGenerator
{
    use BuildMethodSignatures;

    public static function generate($app, string $interfaceName, string|callable $className)
    {
        $instance = null;

        $instanceStr = '$instance = new class($app, $className)
      extends ' . LazyResolver::class . '
      implements ' . $interfaceName . ' {
          ' . self::getMethods($interfaceName) . '
        };';
        eval($instanceStr);
        return $instance;
    }
}

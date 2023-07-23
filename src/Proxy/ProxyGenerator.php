<?php

namespace Edykim\LaravelContainerHelper\Proxy;

use Edykim\LaravelContainerHelper\Traits\BuildMethodSignatures;

class ProxyGenerator
{
    use BuildMethodSignatures;

    public static function generate($app, string $interfaceName, string|callable $proxyClassName)
    {
        $instance = null;

        $instanceStr = '$instance = new class($app, $proxyClassName)
      extends ' . ProxyResolver::class . '
      implements ' . $interfaceName . ' {
          ' . self::getMethods($interfaceName) . '
        };';
        eval($instanceStr);
        return $instance;
    }
}

<?php

namespace Edykim\LaravelContainerHelper\Conditional;

use Edykim\LaravelContainerHelper\Traits\BuildMethodSignatures;
use Edykim\LaravelContainerHelper\Traits\CreateInstance;

class ConditionalGenerator
{
    use BuildMethodSignatures;
    use CreateInstance;

    public static function generate(
        $app,
        string $interfaceName,
        string $conditionName,
        string|callable $instanceAName,
        string|callable $instanceBName,
        string|null $conditionMethodName = null,
    ) {
        $instance = null;

        $condition = $app->make($conditionName);
        $instanceA = self::createInstance($app, $instanceAName);
        $instanceB = self::createInstance($app, $instanceBName);

        if ($conditionMethodName === null) {
            $conditionMethodName = 'check';
        }

        $instanceStr = '$instance = new class(
      $app,
      $condition,
      $conditionMethodName,
      $instanceA,
      $instanceB
    )
      extends ' . ConditionalResolver::class . '
      implements ' . $interfaceName . ' {
          ' . self::getMethods($interfaceName) . '
        };';
        eval($instanceStr);

        return $instance;
    }
}

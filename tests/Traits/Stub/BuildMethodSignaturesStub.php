<?php

namespace Tests\Edykim\LaravelContainerHelper\Traits\Stub;

use Edykim\LaravelContainerHelper\Traits\BuildMethodSignatures;

class BuildMethodSignaturesStub
{
    use BuildMethodSignatures {
        BuildMethodSignatures::getMethods as _getMethods;
    }

    public static function getMethods(string $interfaceName): string
    {
        return static::_getMethods($interfaceName);
    }
}

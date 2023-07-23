<?php

namespace Tests\Edykim\LaravelContainerHelper\Traits\Stub;

use Edykim\LaravelContainerHelper\Traits\CreateInstance;

class CreateInstanceStub
{
    use CreateInstance {
        CreateInstance::createInstance as _createInstance;
    }

    public static function createInstance($app, string|callable $className)
    {
        return static::_createInstance($app, $className);
    }
}

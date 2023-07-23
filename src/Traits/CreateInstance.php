<?php

namespace Edykim\LaravelContainerHelper\Traits;

trait CreateInstance
{
    protected static function createInstance($app, string|callable $className)
    {
        if (is_string($className)) {
            return $app->make($className);
        } elseif (is_callable($className)) {
            return $className($app);
        }
    }
}

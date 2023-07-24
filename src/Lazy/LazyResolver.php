<?php

namespace Edykim\LaravelContainerHelper\Lazy;

use Edykim\LaravelContainerHelper\Traits\CreateInstance;

class LazyResolver
{
    use CreateInstance;

    protected $instance = null;

    public function __construct(protected $app, protected $className)
    {
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ($this->instance === null) {
            $this->instance = self::createInstance($this->app, $this->className);
        }

        return $this->app->call([$this->instance, $name], $arguments);
    }

    public function __debugInfo()
    {
        return [
          "lazy" => $this->className,
        ];
    }
}

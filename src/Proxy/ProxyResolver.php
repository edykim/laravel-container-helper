<?php

namespace Edykim\LaravelContainerHelper\Proxy;

use Edykim\LaravelContainerHelper\Traits\CreateInstance;

class ProxyResolver
{
    use CreateInstance;

    protected $instance = null;

    public function __construct(protected $app, protected $proxyClassName)
    {
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ($this->instance === null) {
            $this->instance = self::createInstance($this->app, $this->proxyClassName);
        }

        return $this->app->call([$this->instance, $name], $arguments);
    }

    public function __debugInfo()
    {
        return [
          "proxy" => $this->proxyClassName,
        ];
    }
}

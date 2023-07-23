<?php

namespace Edykim\LaravelContainerHelper\Conditional;

class ConditionalResolver
{
    public function __construct(
        protected $app,
        protected $conditionInstance,
        protected $conditionMethodName,
        protected $instanceA,
        protected $instanceB,
    ) {
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ($this->app->call([$this->conditionInstance, $this->conditionMethodName], $arguments)) {
            return $this->app->call([$this->instanceA, $name], $arguments);
        } else {
            return $this->app->call([$this->instanceB, $name], $arguments);
        }
    }

    public function __debugInfo()
    {
        return [
          "conditionInstance" => $this->conditionInstance,
          "conditionMethodName" => $this->conditionMethodName,
          "instanceA" => $this->instanceA,
          "instanceB" => $this->instanceB,
        ];
    }
}

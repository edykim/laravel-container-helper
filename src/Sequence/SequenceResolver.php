<?php

namespace Edykim\LaravelContainerHelper\Sequence;

class SequenceResolver
{
    protected array $instances;

    public function __construct(protected $app, ...$instances)
    {
        $this->instances = $instances;
    }

    public function __call(string $name, array $arguments): mixed
    {
        foreach ($this->instances as $instance) {
            $result = $this->app->call([$instance, $name], $arguments);
        }
        return $result;
    }

    public function __debugInfo()
    {
        return [
            "instances" => $this->instances,
        ];
    }
}

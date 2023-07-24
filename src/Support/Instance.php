<?php

namespace Edykim\LaravelContainerHelper\Support;

use Edykim\LaravelContainerHelper\Conditional\ConditionalGenerator;
use Edykim\LaravelContainerHelper\Lazy\LazyGenerator;
use Edykim\LaravelContainerHelper\Sequence\SequenceGenerator;

class Instance
{
    public function __construct(protected string $interfaceName)
    {
    }

    public function lazy(string|callable $implementation)
    {
        return fn ($app) => LazyGenerator::generate($app, $this->interfaceName, $implementation);
    }

    public function when(
        string|array $conditionName,
        string|callable $implementationA,
        string|callable $implementationB
    ) {
        if (is_array($conditionName)) {
            if (count($conditionName) >= 2) {
                $conditionMethodName = $conditionName[1];
            }
            $conditionName = $conditionName[0];
        } else {
            $conditionMethodName = null;
        }

        return fn ($app) => ConditionalGenerator::generate(
            $app,
            $this->interfaceName,
            $conditionName,
            $implementationA,
            $implementationB,
            $conditionMethodName,
        );
    }

    public function sequence(string|callable ...$implementations)
    {
        return fn ($app) => SequenceGenerator::generate(
            $app,
            $this->interfaceName,
            ...$implementations
        );
    }

    public function with(callable $callable)
    {
        return $callable($this);
    }
}

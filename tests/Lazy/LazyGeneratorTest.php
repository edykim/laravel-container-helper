<?php

namespace Tests\Edykim\LaravelContainerHelper\Lazy;

use Edykim\LaravelContainerHelper\Lazy\LazyGenerator;
use Edykim\LaravelContainerHelper\Lazy\LazyResolver;
use Tests\Edykim\LaravelContainerHelper\Stub\RuleInterface;
use Tests\Edykim\LaravelContainerHelper\Stub\StandardRule;
use Tests\Edykim\LaravelContainerHelper\TestCase;

class LazyGeneratorTest extends TestCase
{
    public function test_instance()
    {
        $appMock = new class () {
        };

        $instance = LazyGenerator::generate(
            $appMock,
            RuleInterface::class,
            StandardRule::class,
        );

        $this->assertInstanceOf(LazyResolver::class, $instance);
        $this->assertInstanceOf(RuleInterface::class, $instance);
    }

    public function test_instance_lazy()
    {
        $appMock = new class () {
            public array $made = [];
            public array $called = [];
            public function make(string $interfaceName)
            {
                $obj = new $interfaceName();
                $this->made[] = $obj;
                return $obj;
            }

            public function call(array $called, array $arguments)
            {
                $this->called[] = [$called, $arguments];
                call_user_func($called, ...$arguments);
            }
        };

        /** @var RuleInterface $instance */
        $instance = LazyGenerator::generate(
            $appMock,
            RuleInterface::class,
            StandardRule::class,
        );

        $beforeExecute = count($appMock->made);

        $instance->apply();

        // will not instantiate any object until it actually is being called
        $afterExecute = count($appMock->made);
        $expected = [
          [[$appMock->made[0], 'apply'], [],],
        ];

        $this->assertEquals(0, $beforeExecute);
        $this->assertEquals(1, $afterExecute);

        $this->assertEquals($expected, $appMock->called);
    }
}

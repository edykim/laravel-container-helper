<?php

namespace Tests\Edykim\LaravelContainerHelper\Sequence;

use Edykim\LaravelContainerHelper\Sequence\SequenceGenerator;
use Edykim\LaravelContainerHelper\Sequence\SequenceResolver;
use Tests\Edykim\LaravelContainerHelper\Stub\CalculatorInterface;
use Tests\Edykim\LaravelContainerHelper\Stub\Cart;
use Tests\Edykim\LaravelContainerHelper\Stub\HandlingChargeCalculator;
use Tests\Edykim\LaravelContainerHelper\Stub\ProductCalculator;
use Tests\Edykim\LaravelContainerHelper\Stub\RuleInterface;
use Tests\Edykim\LaravelContainerHelper\Stub\StandardRule;
use Tests\Edykim\LaravelContainerHelper\Stub\Summary;
use Tests\Edykim\LaravelContainerHelper\TestCase;

class SequenceGeneratorTest extends TestCase
{
    public function test_instance()
    {
        $appMock = new class () {
        };

        $instance = SequenceGenerator::generate(
            $appMock,
            RuleInterface::class,
        );

        $this->assertInstanceOf(SequenceResolver::class, $instance);
        $this->assertInstanceOf(RuleInterface::class, $instance);
    }

    public function test_instance_with_sequence()
    {
        $appMock = new class () {
            public array $made = [];
            public function make(string $interfaceName)
            {
                $obj = new $interfaceName();
                $this->made[] = $obj;
                return $obj;
            }
        };

        SequenceGenerator::generate(
            $appMock,
            RuleInterface::class,
            StandardRule::class,
            StandardRule::class,
        );

        $this->assertInstanceOf(StandardRule::class, $appMock->made[0]);
        $this->assertInstanceOf(StandardRule::class, $appMock->made[1]);
    }

    public function test_instance_with_sequencial_execution()
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
        $instance = SequenceGenerator::generate(
            $appMock,
            RuleInterface::class,
            StandardRule::class,
            StandardRule::class,
        );

        $expected = [
          [[$appMock->made[0], 'apply'], [],],
          [[$appMock->made[1], 'apply'], [],],
        ];

        $beforeCount0 = $appMock->made[0]->count;
        $beforeCount1 = $appMock->made[1]->count;
        $beforeCalled = count($appMock->called);

        $instance->apply();

        $afterCount0 = $appMock->made[0]->count;
        $afterCount1 = $appMock->made[1]->count;
        $afterCalled = count($appMock->called);

        $this->assertEquals(0, $beforeCount0);
        $this->assertEquals(0, $beforeCount1);

        $this->assertEquals(1, $afterCount0);
        $this->assertEquals(1, $afterCount1);

        $this->assertEquals(0, $beforeCalled);
        $this->assertEquals(2, $afterCalled);
        $this->assertEquals($expected, $appMock->called);
    }

    public function test_instance_with_sequencial_calculation()
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
                return call_user_func($called, ...$arguments);
            }
        };

        /** @var CalculatorInterface $instance */
        $instance = SequenceGenerator::generate(
            $appMock,
            CalculatorInterface::class,
            ProductCalculator::class,
            HandlingChargeCalculator::class,
        );

        $cart = new Cart([100, 200, 300]);
        $summary = new Summary();

        $instance->calculate($cart, $summary);
        $this->assertEquals(610, $summary->total);
    }
}

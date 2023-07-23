<?php

namespace Tests\Edykim\LaravelContainerHelper\Conditional;

use Edykim\LaravelContainerHelper\Conditional\ConditionalGenerator;
use Edykim\LaravelContainerHelper\Conditional\ConditionalResolver;
use Tests\Edykim\LaravelContainerHelper\Conditional\Conditions\AlwaysFalse;
use Tests\Edykim\LaravelContainerHelper\Conditional\Conditions\AlwaysTrue;
use Tests\Edykim\LaravelContainerHelper\Stub\RuleInterface;
use Tests\Edykim\LaravelContainerHelper\Stub\SpecialRule;
use Tests\Edykim\LaravelContainerHelper\Stub\StandardRule;
use Tests\Edykim\LaravelContainerHelper\TestCase;

class ConditionalGeneratorTest extends TestCase
{
    public function test_instance()
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

        $instance = ConditionalGenerator::generate(
            $appMock,
            RuleInterface::class,
            AlwaysTrue::class,
            StandardRule::class,
            SpecialRule::class,
        );

        $this->assertInstanceOf(ConditionalResolver::class, $instance);
        $this->assertInstanceOf(RuleInterface::class, $instance);
    }

    public function test_instance_with_condition_true()
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

        /** @var RuleInterface $instance */
        $instance = ConditionalGenerator::generate(
            $appMock,
            RuleInterface::class,
            AlwaysTrue::class,
            StandardRule::class,
            SpecialRule::class,
        );

        $beforeConditionChecked = $appMock->made[0]->count;
        $beforeTrueCalled = $appMock->made[1]->count;
        $beforeFalseCalled = $appMock->made[2]->count;

        $instance->apply();

        $afterConditionChecked = $appMock->made[0]->count;
        $afterTrueCalled = $appMock->made[1]->count;
        $afterFalseCalled = $appMock->made[2]->count;

        $this->assertEquals(3, count($appMock->made));
        $this->assertEquals(0, $beforeConditionChecked);
        $this->assertEquals(0, $beforeTrueCalled);
        $this->assertEquals(0, $beforeFalseCalled);

        $this->assertEquals(1, $afterConditionChecked);
        $this->assertEquals(1, $afterTrueCalled);
        $this->assertEquals(0, $afterFalseCalled);
    }

    public function test_instance_with_condition_false()
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

        /** @var RuleInterface $instance */
        $instance = ConditionalGenerator::generate(
            $appMock,
            RuleInterface::class,
            AlwaysFalse::class,
            StandardRule::class,
            SpecialRule::class,
        );

        $beforeConditionChecked = $appMock->made[0]->count;
        $beforeTrueCalled = $appMock->made[1]->count;
        $beforeFalseCalled = $appMock->made[2]->count;

        $instance->apply();

        $afterConditionChecked = $appMock->made[0]->count;
        $afterTrueCalled = $appMock->made[1]->count;
        $afterFalseCalled = $appMock->made[2]->count;

        $this->assertEquals(3, count($appMock->made));
        $this->assertEquals(0, $beforeConditionChecked);
        $this->assertEquals(0, $beforeTrueCalled);
        $this->assertEquals(0, $beforeFalseCalled);

        $this->assertEquals(1, $afterConditionChecked);
        $this->assertEquals(0, $afterTrueCalled);
        $this->assertEquals(1, $afterFalseCalled);
    }
}

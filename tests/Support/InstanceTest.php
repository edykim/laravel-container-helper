<?php

namespace Tests\Edykim\LaravelContainerHelper\Support;

use Edykim\LaravelContainerHelper\Conditional\ConditionalResolver;
use Edykim\LaravelContainerHelper\Proxy\ProxyResolver;
use Edykim\LaravelContainerHelper\Sequence\SequenceResolver;
use Edykim\LaravelContainerHelper\Support\Instance;
use Tests\Edykim\LaravelContainerHelper\Conditional\Conditions\AlwaysFalse;
use Tests\Edykim\LaravelContainerHelper\Conditional\Conditions\AlwaysTrue;
use Tests\Edykim\LaravelContainerHelper\Stub\DefaultRule;
use Tests\Edykim\LaravelContainerHelper\Stub\PremiumRule;
use Tests\Edykim\LaravelContainerHelper\Stub\RuleInterface;
use Tests\Edykim\LaravelContainerHelper\Stub\SpecialRule;
use Tests\Edykim\LaravelContainerHelper\Stub\StandardRule;
use Tests\Edykim\LaravelContainerHelper\TestCase;

use function Edykim\LaravelContainerHelper\Support\instance;

class InstanceTest extends TestCase
{
    protected $appMock;

    public function setUp(): void
    {
        $this->appMock = new class () {
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
    }

    public function test_instance_proxy()
    {
        $callable = (new Instance(RuleInterface::class))->proxy(StandardRule::class);

        /** @var RuleInterface|ProxyResolver $instance */
        $instance = $callable($this->appMock);

        $this->assertIsCallable($callable);
        $this->assertInstanceOf(RuleInterface::class, $instance);
        $this->assertInstanceOf(ProxyResolver::class, $instance);
        $this->assertEquals(StandardRule::class, $instance->__debugInfo()['proxy']);
    }

    public function test_instance_conditional()
    {
        $callable = (new Instance(RuleInterface::class))->when(
            AlwaysTrue::class,
            StandardRule::class,
            SpecialRule::class
        );

        /** @var RuleInterface|ConditionalResolver $instance */
        $instance = $callable($this->appMock);
        $debugInfo = $instance->__debugInfo();

        $this->assertIsCallable($callable);
        $this->assertInstanceOf(RuleInterface::class, $instance);
        $this->assertInstanceOf(ConditionalResolver::class, $instance);

        $this->assertInstanceOf(AlwaysTrue::class, $debugInfo['conditionInstance']);
        $this->assertEquals('check', $debugInfo['conditionMethodName']);

        $this->assertInstanceOf(StandardRule::class, $debugInfo['instanceA']);
        $this->assertInstanceOf(SpecialRule::class, $debugInfo['instanceB']);
    }

    public function test_instance_sequence()
    {
        $callable = (new Instance(RuleInterface::class))->sequence(
            StandardRule::class,
            SpecialRule::class,
            SpecialRule::class,
            StandardRule::class,
        );

        /** @var RuleInterface|SequenceResolver $instance */
        $instance = $callable($this->appMock);
        $instances = $instance->__debugInfo()['instances'];

        $this->assertIsCallable($callable);
        $this->assertInstanceOf(RuleInterface::class, $instance);
        $this->assertInstanceOf(SequenceResolver::class, $instance);

        $this->assertInstanceOf(StandardRule::class, $instances[0]);
        $this->assertInstanceOf(SpecialRule::class, $instances[1]);
        $this->assertInstanceOf(SpecialRule::class, $instances[2]);
        $this->assertInstanceOf(StandardRule::class, $instances[3]);
    }

    public function test_instance_nested()
    {
        $inst = instance(RuleInterface::class);
        $callable = $inst->with(
            fn (Instance $inst) =>
            $inst->sequence(
                $inst->when(
                    AlwaysTrue::class,
                    $inst->proxy($inst->when(
                        AlwaysFalse::class,
                        SpecialRule::class,
                        StandardRule::class, // should be called
                    )),
                    PremiumRule::class,
                ),
                DefaultRule::class, // should be called
            )
        );

        /** @var RuleInterface|SequenceResolver $instance */
        $instance = $callable($this->appMock);
        $this->assertIsCallable($callable);
        $instance->apply();

        $this->assertInstanceOf(RuleInterface::class, $instance);
        $this->assertInstanceOf(SequenceResolver::class, $instance);

        $standardRuleFound = array_values(array_filter(
            $this->appMock->made,
            fn ($d) => $d instanceof StandardRule
        ));
        $specialRuleFound = array_values(array_filter(
            $this->appMock->made,
            fn ($d) => $d instanceof SpecialRule
        ));
        $premiumRuleFound = array_values(array_filter(
            $this->appMock->made,
            fn ($d) => $d instanceof PremiumRule
        ));
        $defaultRuleFound = array_values(array_filter(
            $this->appMock->made,
            fn ($d) => $d instanceof DefaultRule
        ));

        $this->assertCount(1, $standardRuleFound);
        $this->assertCount(1, $specialRuleFound);
        $this->assertCount(1, $premiumRuleFound);
        $this->assertCount(1, $defaultRuleFound);

        $this->assertEquals(1, $standardRuleFound[0]->count);
        $this->assertEquals(0, $specialRuleFound[0]->count);
        $this->assertEquals(0, $premiumRuleFound[0]->count);
        $this->assertEquals(1, $defaultRuleFound[0]->count);
    }
}

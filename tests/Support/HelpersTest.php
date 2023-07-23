<?php

namespace Tests\Edykim\LaravelContainerHelper\Support;

use Edykim\LaravelContainerHelper\Support\Instance;
use Tests\Edykim\LaravelContainerHelper\TestCase;

use function Edykim\LaravelContainerHelper\Support\instance;

class HelpersTest extends TestCase
{
    public function test_helper_instance()
    {
        $actual = instance('SomeInterfaceName');
        $this->assertInstanceOf(Instance::class, $actual);
        $this->assertEquals(new Instance('SomeInterfaceName'), $actual);
    }
}

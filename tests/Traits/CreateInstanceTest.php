<?php

namespace Tests\Edykim\LaravelContainerHelper\Traits;

use Tests\Edykim\LaravelContainerHelper\TestCase;
use Tests\Edykim\LaravelContainerHelper\Traits\Stub\CreateInstanceStub;

class CreateInstanceTest extends TestCase
{
    public function test_class_name_creation_with_string_name()
    {
        $appMock = new class () {
            public ?string $made = null;
            public function make(string $interfaceName)
            {
                $this->made = $interfaceName;
                return $this;
            }
        };

        $instance = CreateInstanceStub::createInstance($appMock, 'SomeClassName');

        $this->assertEquals($appMock, $instance);
        $this->assertEquals('SomeClassName', $appMock->made);
    }

    public function test_class_name_creation_with_callable()
    {
        $appMock = new class () {
            public ?string $made = null;
            public function make(string $interfaceName)
            {
                $this->made = $interfaceName;
                return $this;
            }
        };

        $called = false;
        $appBeingCalled = null;

        $instance = CreateInstanceStub::createInstance(
            $appMock,
            function ($app) use (&$called, &$appBeingCalled) {
                $called = true;
                $appBeingCalled = $app;
                return 'result-of-instance';
            }
        );

        $this->assertTrue($called);
        $this->assertEquals($appMock, $appBeingCalled);
        $this->assertEquals('result-of-instance', $instance);
    }
}

<?php

namespace Tests\Edykim\LaravelContainerHelper\Traits;

use Tests\Edykim\LaravelContainerHelper\TestCase;
use Tests\Edykim\LaravelContainerHelper\Traits\Contracts\ComplexInterface;
use Tests\Edykim\LaravelContainerHelper\Traits\Contracts\EmptyInterface;
use Tests\Edykim\LaravelContainerHelper\Traits\Stub\BuildMethodSignaturesStub;

class BuildMethodSignaturesTest extends TestCase
{
    public function test_empty_interface()
    {
        $method = BuildMethodSignaturesStub::getMethods(EmptyInterface::class);
        $this->assertEquals('', $method);
    }

    public function test_complex_interface()
    {
        $expected = file_get_contents(__DIR__ .'/expected/result.complex.txt');
        $method = BuildMethodSignaturesStub::getMethods(ComplexInterface::class);
        $this->assertEquals($expected, $method);
    }
}

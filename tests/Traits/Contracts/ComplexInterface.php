<?php

namespace Tests\Edykim\LaravelContainerHelper\Traits\Contracts;

interface ComplexInterface
{
    public function method_01();
    public function method_02(): void;
    public function method_03(): int;
    public function method_04(): string;
    public function method_05(): EmptyInterface;
    public function method_06(): EmptyInterface & ComplexInterface;
    public function method_07(): EmptyInterface | ComplexInterface;
    public function method_08(): (EmptyInterface & ComplexInterface) | OtherEmptyInterface;

    public function method_11($a);
    public function method_12(int $a);
    public function method_13(int|string $a);
    public function method_14(int $a, $b);
    public function method_15(int|string $a, int $b);
    public function method_16(EmptyInterface & ComplexInterface $a);
    public function method_17(EmptyInterface | ComplexInterface $a);
    public function method_18((EmptyInterface & ComplexInterface) | OtherEmptyInterface $a);
    public function method_19(EmptyInterface &$a);

    public function method_21($a): int;
    public function method_22(int $a): int|string;
    public function method_23(int|string $a): EmptyInterface & ComplexInterface;
    public function method_24((EmptyInterface & ComplexInterface) | OtherEmptyInterface $a): (EmptyInterface & ComplexInterface) | OtherEmptyInterface;
}

<?php

namespace Tests\Edykim\LaravelContainerHelper\Stub;

interface CalculatorInterface
{
    public function calculate(Cart $cart, Summary $summary): void;
}

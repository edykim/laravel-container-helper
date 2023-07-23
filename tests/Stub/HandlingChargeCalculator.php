<?php

namespace Tests\Edykim\LaravelContainerHelper\Stub;

class HandlingChargeCalculator implements CalculatorInterface
{
    public function calculate(Cart $cart, Summary $summary): void
    {
        $summary->total += 10;
    }
}

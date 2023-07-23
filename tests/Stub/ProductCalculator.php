<?php

namespace Tests\Edykim\LaravelContainerHelper\Stub;

class ProductCalculator implements CalculatorInterface
{
    public function calculate(Cart $cart, Summary $summary): void
    {
        foreach($cart->items as $item) {
            $summary->total += $item;
        }
    }
}

<?php

namespace Tests\Edykim\LaravelContainerHelper\Stub;

class PremiumRule implements RuleInterface
{
    public int $count = 0;

    public function apply(): void
    {
        $this->count += 1;
    }
}

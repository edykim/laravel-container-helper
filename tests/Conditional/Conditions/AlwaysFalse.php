<?php

namespace Tests\Edykim\LaravelContainerHelper\Conditional\Conditions;

class AlwaysFalse
{
    public int $count = 0;
    public function check(): bool
    {
        $this->count += 1;
        return false;
    }
}

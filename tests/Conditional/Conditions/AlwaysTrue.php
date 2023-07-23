<?php

namespace Tests\Edykim\LaravelContainerHelper\Conditional\Conditions;

class AlwaysTrue
{
    public int $count = 0;
    public function check(): bool
    {
        $this->count += 1;
        return true;
    }
}

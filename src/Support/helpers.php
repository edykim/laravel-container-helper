<?php

namespace Edykim\LaravelContainerHelper\Support;

function instance(string $interfaceName)
{
    return new Instance($interfaceName);
}

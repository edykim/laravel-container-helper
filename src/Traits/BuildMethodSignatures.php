<?php

namespace Edykim\LaravelContainerHelper\Traits;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

trait BuildMethodSignatures
{
    protected static function getMethods(string $interfaceName): string
    {
        $class = new ReflectionClass($interfaceName);

        $methods = $class->getMethods();
        $result = '';
        foreach ($methods as $method) {
            $name = $method->getName();
            $params = $method->getParameters();
            $returnType = $method->getReturnType();
            if ($returnType) {
                $returnTypeStr = ' : ' . self::getTypeSignature($returnType);
            } else {
                $returnTypeStr = '';
            }
            $parameters = [];
            $typeNames = [];

            foreach ($params as $param) {
                $type = $param->getType();
                $typeName = $type ? (self::getTypeSignature($type) . ' ') : '';
                $isReference = $param->isPassedByReference() ? '&' : '';
                $parameters[] = $typeName . $isReference . '$' . $param->getName();
                $typeNames[] = $param->getName();
            }

            $typesStr = implode(', ', $parameters);
            $method = "public function $name(" . $typesStr . ")$returnTypeStr {" . PHP_EOL;
            $params = implode(', ', array_map(fn ($d) => "'$d'", $typeNames));
            $args = 'array_combine([' . $params . '], func_get_args())';
            if ($returnTypeStr === ' : void') {
                $method .= "\t" . '$this->__call(__FUNCTION__, ' . $args . ');' . PHP_EOL;
            } else {
                $method .= "\t" . 'return $this->__call(__FUNCTION__, ' . $args . ');' . PHP_EOL;
            }
            $method .= "}" . PHP_EOL;
            $result .= $method;
        }

        return $result;
    }

    protected static function getTypeSignature($type): string
    {
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        } elseif (
            $type instanceof ReflectionUnionType
            || $type instanceof ReflectionIntersectionType
        ) {
            $types = array_map(fn ($type) => self::getTypeSignature($type), $type->getTypes());
            $separator = $type instanceof ReflectionUnionType ? ' | ' : ' & ';
            return '(' . implode($separator, $types) . ')';
        }

        return '';
    }
}

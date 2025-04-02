<?php

namespace Municipio\SchemaData\Utils;

use ReflectionClass;
use ReflectionMethod;
use Municipio\Schema\BaseType;

class GetSchemaPropertiesWithParamTypes implements GetSchemaPropertiesWithParamTypesInterface
{
    public function getSchemaPropertiesWithParamTypes(string $schemaType): array
    {
        $baseTypeMethods   = $this->getSchemaPropertiesFromClass(BaseType::class);
        $schemaTypeMethods = $this->getSchemaPropertiesFromClass($schemaType);

        // Remove the properties that are already in the base type. Properties are on array key.
        return array_diff_key($schemaTypeMethods, $baseTypeMethods);
    }

    private function getSchemaPropertiesFromClass(string $schemaType): array
    {
        $reflection = new ReflectionClass($schemaType);
        $methods    = $reflection->getMethods();
        $properties = [];

        foreach ($methods as $method) {
            if ($method->getName() !== '__construct' && strpos($method->getName(), ' ') === false) {
                $properties[$method->getName()] = $this->getParamTypesFromDocblock($method) ?: ['string'];
            }
        }

        return $properties;
    }

    private function getParamTypesFromDocblock(ReflectionMethod $method): array
    {
        $docblock = $method->getDocComment();
        $matches  = [];
        preg_match('/@param\s+([^\s]+)/', $docblock, $matches);

        $params = explode('|', $matches[1] ?? '');
        return array_map(fn ($param) => $this->sanitizeParamType($param), $params);
    }

    private function sanitizeParamType(string $type): string
    {
        $re    = '/^(\\\\Municipio\\\\Schema).+(Contracts\\\\)(.+)(Contract)(\[])?$/';
        $subst = "$3$5";
        return preg_replace($re, $subst, $type);
    }
}

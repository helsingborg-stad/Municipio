<?php

namespace Municipio\SchemaData\Utils;

use ReflectionClass;
use ReflectionMethod;
use Municipio\Schema\BaseType;

/**
 * Class GetSchemaPropertiesWithParamTypes
 *
 * This class is responsible for retrieving schema properties and their parameter types.
 */
class GetSchemaPropertiesWithParamTypes implements GetSchemaPropertiesWithParamTypesInterface
{
    /**
     * Get schema properties with their parameter types.
     *
     * @param string $schemaType The schema type to retrieve properties for.
     *
     * @return array An associative array of schema properties and their parameter types.
     */
    public function getSchemaPropertiesWithParamTypes(string $schemaType): array
    {
        $baseTypeMethods   = $this->getSchemaPropertiesFromClass(BaseType::class);
        $schemaTypeMethods = $this->getSchemaPropertiesFromClass($schemaType);

        // Remove the properties that are already in the base type. Properties are on array key.
        return array_diff_key($schemaTypeMethods, $baseTypeMethods);
    }

    /**
     * Get schema properties from a class.
     *
     * @param string $schemaType The schema type to retrieve properties for.
     *
     * @return array An associative array of schema properties and their parameter types.
     */
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

    /**
     * Get parameter types from the docblock of a method.
     *
     * @param ReflectionMethod $method The method to retrieve parameter types from.
     *
     * @return array An array of parameter types.
     */
    private function getParamTypesFromDocblock(ReflectionMethod $method): array
    {
        $docblock = $method->getDocComment();
        $matches  = [];
        preg_match('/@param\s+([^\s]+)/', $docblock, $matches);

        $params = explode('|', $matches[1] ?? '');
        return array_map(fn ($param) => $this->sanitizeParamType($param), $params);
    }

    /**
     * Sanitize the parameter type by removing unnecessary parts.
     *
     * @param string $type The parameter type to sanitize.
     *
     * @return string The sanitized parameter type.
     */
    private function sanitizeParamType(string $type): string
    {
        $re    = '/^(\\\\Municipio\\\\Schema).+(Contracts\\\\)(.+)(Contract)(\[])?$/';
        $subst = "$3$5";
        return preg_replace($re, $subst, $type);
    }
}

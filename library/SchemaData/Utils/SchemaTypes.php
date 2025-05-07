<?php

namespace Municipio\SchemaData\Utils;

use Municipio\Schema\Thing;
use Municipio\SchemaData\Utils\Contracts\SchemaTypesInterface;

/**
 * SchemaTypes.
 *
 * Class to get all schema types.
 * A schema type is a type of schema that can be used in the schema.org vocabulary.
 */
class SchemaTypes implements SchemaTypesInterface
{
    /**
     * SchemaTypes constructor.
     */
    public function getSchemaTypes(): array
    {
        static $schemaTypes = null;

        if ($schemaTypes === null) {
            $schemaTypes = $this->getSchemaTypesFromFiles();
        }

        return $schemaTypes;
    }

    /**
     * Get all schema types from the files in the same folder as the Thing class.
     *
     * @return string[]
     */
    private function getSchemaTypesFromFiles(): array
    {
        // Find folder where the Thing class is located
        $path = dirname((new \ReflectionClass(new Thing()))->getFileName());

        // Get all PHP files in the folder
        return array_map(function ($file) {
            return basename($file, '.php');
        }, glob($path . '/*.php'));
    }
}

<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use Municipio\Schema\Schema;
use WpService\Contracts\ApplyFilters;

/**
 * Class GetAcfFieldGroupBySchemaType
 *
 * @package Municipio\SchemaData\SchemaPropertiesForm
 */
class GetAcfFieldGroupBySchemaType implements GetAcfFieldGroupBySchemaTypeInterface
{
    /**
     * GetAcfFieldGroupBySchemaType constructor.
     *
     * @param ApplyFilters $wpService
     * @param GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes
     * @param GetFormFieldsBySchemaPropertiesInterface $getFormFieldsBySchemaProperties
     */
    public function __construct(
        private ApplyFilters $wpService,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes,
        private GetFormFieldsBySchemaPropertiesInterface $getFormFieldsBySchemaProperties
    ) {
    }

    /**
     * Get ACF field group by schema type.
     *
     * @param string $schemaType
     *
     * @return array
     */
    public function getAcfFieldGroup(string $schemaType): array
    {
        if (!$this->isValidSchemaType($schemaType)) {
            return [];
        }

        $schemaObject     = Schema::$schemaType();
        $schemaProperties = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schemaObject::class);

        return [
            'instructions' => 'Schema properties for ' . $schemaType,
            'key'          => 'schema_properties',
            'title'        => "Schema properties for {$schemaType}",
            'fields'       => $this->getFormFieldsBySchemaProperties->getFormFieldsBySchemaProperties($schemaType, $schemaProperties),
            'location'     => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'all',
                    ],
                ],
            ],
        ];
    }

    /**
     * Check if the schema type is valid.
     *
     * @param string $schemaType
     *
     * @return bool
     */
    private function isValidSchemaType(string $schemaType): bool
    {
        return !empty($schemaType) && method_exists(Schema::class, $schemaType);
    }
}

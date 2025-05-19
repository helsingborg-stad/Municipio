<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\SchemaPropertyHandlerInterface;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;

class SchemaPropertiesFromMappedFields implements SchemaPropertiesFromMappedFieldsInterface
{
    /**
     * Constructor.
     *
     * @param GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypesService The service to get schema properties with param types.
     * @param SchemaPropertyHandlerInterface[] $propertyHandlers The property handlers.
     */
    public function __construct(
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypesService,
        private array $propertyHandlers
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply(BaseType $schema, array $mappedFields): BaseType
    {
        $schemaProperties = $this->getSchemaPropertiesWithParamTypesService->getSchemaPropertiesWithParamTypes($schema::class);
        $schemaProperties = [...$schemaProperties, '@id' => ['string']];

        foreach ($mappedFields as $mappedField) {
            $value        = $mappedField->getValue();
            $fieldType    = $mappedField->getType() ?? '';
            $propertyName = $mappedField->getName();

            if (is_string($value) && json_validate(stripslashes($value))) {
                $value = json_decode(stripslashes($value), true);
            }

            if (!array_key_exists($propertyName, $schemaProperties)) {
                continue;
            }

            $propertyTypes = $schemaProperties[$propertyName];

            foreach ($this->propertyHandlers as $handler) {
                $supports = $handler->supports($propertyName, $fieldType, $value, $propertyTypes);
                if ($supports) {
                    $schema = $handler->handle($schema, $propertyName, $value);
                    break;
                }
            }
        }

        return $schema;
    }
}

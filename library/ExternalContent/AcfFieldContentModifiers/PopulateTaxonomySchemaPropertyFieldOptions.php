<?php

namespace Municipio\ExternalContent\AcfFieldContentModifiers;

use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierInterface;
use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;

class PopulateTaxonomySchemaPropertyFieldOptions implements AcfFieldContentModifierInterface
{
    public function __construct(private string $fieldKey, private GetEnabledSchemaTypesInterface $getEnabledSchemaTypes)
    {
    }

    public function modifyFieldContent(array $field): array
    {
        $enabledProperties = $this->getEnabledSchemaTypes->getEnabledSchemaTypesAndProperties();
        $enabledProperties = array_map(function ($item) {
            if (!empty($item)) {
                $subItemsWithNamedKeys = [];
                foreach ($item as $subItem) {
                    $subItemsWithNamedKeys[$subItem] = $subItem;
                }
                return $subItemsWithNamedKeys;
            }

            return $item;
        }, $enabledProperties);

        $field['choices'] = array_filter($enabledProperties, fn($item) => !empty($item));

        return $field;
    }

    public function getFieldKey(): string
    {
        return $this->fieldKey;
    }
}

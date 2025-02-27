<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Spatie\SchemaOrg\BaseType;

/**
 * Add all PropertyValue objects found in custom property @meta as meta data in post.
 */
class MetaPropertyValueDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * Class constructor.
     *
     * @param WpPostArgsFromSchemaObjectInterface $inner The inner WpPostArgsFromSchemaObjectInterface instance.
     */
    public function __construct(private ?WpPostArgsFromSchemaObjectInterface $inner = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType $schemaObject): array
    {
        $postArgs     = [];
        $propertyName = '@meta';

        if ($this->inner !== null) {
            $postArgs = $this->inner->create($schemaObject);
        }

        if ($schemaObject->hasProperty($propertyName) && is_array($schemaObject->getProperty($propertyName))) {
            if (!isset($postArgs['meta_input'])) {
                $postArgs['meta_input'] = [];
            }

            $metaProperties = array_filter(
                $schemaObject->getProperty($propertyName),
                fn($metaProperty) =>
                    $metaProperty instanceof BaseType &&
                    !empty($metaProperty->getProperty('name')) &&
                    !empty($metaProperty->getProperty('value'))
            );

            if (empty($metaProperties)) {
                return $postArgs;
            }

            foreach ($metaProperties as $metaProperty) {
                $name                          = $metaProperty->getProperty('name');
                $value                         = $metaProperty->getProperty('value');
                $postArgs['meta_input'][$name] = $value;
            }
        }

        return $postArgs;
    }
}

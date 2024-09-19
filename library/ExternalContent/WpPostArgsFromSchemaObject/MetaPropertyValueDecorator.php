<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\JobPosting;

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
    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $postArgs = [];

        if ($this->inner !== null) {
            $postArgs = $this->inner->create($schemaObject, $source);
        }

        if ($schemaObject->hasProperty('@meta') && is_array($schemaObject->getProperty('@meta'))) {
            if (!isset($postArgs['meta_input'])) {
                $postArgs['meta_input'] = [];
            }

            $metaProperties = array_filter(
                $schemaObject->getProperty('@meta'),
                fn($metaProperty) =>
                    $metaProperty instanceof BaseType &&
                    !empty($metaProperty->getProperty('name')) &&
                    !empty($metaProperty->getProperty('value'))
            );

            foreach ($metaProperties as $metaProperty) {
                $name                          = $metaProperty->getProperty('name');
                $value                         = $metaProperty->getProperty('value');
                $postArgs['meta_input'][$name] = $value;
            }
        }

        return $postArgs;
    }
}

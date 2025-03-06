<?php

namespace Municipio\ExternalContent\Config;

/**
 * Class SourceTaxonomyConfig
 */
class SourceTaxonomyConfig implements SourceTaxonomyConfigInterface
{
    /**
     * SourceTaxonomyConfig constructor.
     *
     * @param string $schemaObjectType
     * @param string $fromSchemaProperty
     * @param string $pluralName
     * @param string $singularName
     * @param bool $hierarchical
     */
    public function __construct(
        private string $schemaObjectType,
        private string $fromSchemaProperty,
        private string $pluralName,
        private string $singularName,
        private bool $hierarchical
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFromSchemaProperty(): string
    {
        return $this->fromSchemaProperty;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        // Prepend the schema object type to the schema object property.
        $name = $this->schemaObjectType . '_' . $this->getFromSchemaProperty();

        // Replace . with underscore in the schema object property.
        $name = str_replace('.', '_', $name);

        // Remove special characters from the schema object property. Allow underscores.
        $name = preg_replace(
            '/[^a-zA-Z0-9_]/',
            '',
            $name
        );

        // Convert the schema object property to a taxonomy name by making it snake case from camelcase.
        $name = strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0', $name)
        );

        // Ensure that the taxonomy name is not longer than 32 characters.
        return substr($name, 0, 32);
    }

    /**
     * @inheritDoc
     */
    public function getPluralName(): string
    {
        return $this->pluralName;
    }

    /**
     * @inheritDoc
     */
    public function getSingularName(): string
    {
        return $this->singularName;
    }

    /**
     * @inheritDoc
     */
    public function isHierarchical(): bool
    {
        return $this->hierarchical;
    }
}

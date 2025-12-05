<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

/**
 * Taxonomy class represents a custom taxonomy in WordPress.
 */
class Taxonomy implements TaxonomyInterface
{
    /**
     * Constructor for the Taxonomy class.
     */
    public function __construct(
        private string $schemaType,
        private string $schemaProperty,
        private array $objectTypes,
        private string $label,
        private string $singularLabel,
        private array $arguments = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getPreparedName();
    }

    /**
     * Prepares the taxonomy name based on the schema type and property.
     *
     * @return string
     */
    private function getPreparedName(): string
    {
        // Prepend the schema object type to the schema object property.
        $name = $this->getSchemaType() . '_' . $this->getSchemaProperty();

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
    public function getArguments(): array
    {
        return $this->buildArgumentsArray();
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function getSingularLabel(): string
    {
        return $this->singularLabel;
    }

    /**
     * Builds the arguments array for the taxonomy registration.
     *
     * @return array
     */
    private function buildArgumentsArray(): array
    {
        return array_merge(
            [
                'labels'       => [
                    'name'          => $this->label,
                    'singular_name' => $this->singularLabel,
                ],
                'public'       => true,
                'show_in_rest' => true,
                'hierarchical' => false,
            ],
            $this->arguments
        );
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypes(): array
    {
        return $this->objectTypes;
    }

    /**
     * @inheritDoc
     */
    public function getSchemaType(): string
    {
        return $this->schemaType;
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(): string
    {
        return $this->schemaProperty;
    }

    /**
     * Default implementation: return value as-is.
     */
    public function formatTermValue(mixed $value, array $schema): string|array|null
    {
        return $value;
    }
}

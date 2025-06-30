<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

interface TaxonomyInterface
{
    /**
     * Get the taxonomy name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the arguments for the taxonomy registration.
     *
     * @return array
     */
    public function getArguments(): array;

    /**
     * Get the plural label for the taxonomy.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get the singular label for the taxonomy.
     *
     * @return string
     */
    public function getSingularLabel(): string;
}

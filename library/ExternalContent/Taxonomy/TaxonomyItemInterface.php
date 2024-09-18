<?php

namespace Municipio\ExternalContent\Taxonomy;

/**
     * Interface for taxonomy items.
     */
interface TaxonomyItemInterface
{
    /**
     * Get the schema object type.
     *
     * @return string The schema object type.
     */
    public function getSchemaObjectType(): string;

    /**
     * Get the post types for which the taxonomy is registered.
     */
    public function getPostTypes(): array;

    /**
     * Get the schema object property from which a taxonomy is populated.
     *
     * @return string The schema object property.
     */
    public function getSchemaObjectProperty(): string;

    /**
     * Get the name of the taxonomy.
     *
     * @return string The name of the taxonomy.
     */
    public function getName(): string;

    /**
     * Get the single label for the taxonomy.
     *
     * @return string The single label for the taxonomy.
     */
    public function getSingleLabel(): string;

    /**
     * Get the plural label for the taxonomy.
     *
     * @return string The plural label for the taxonomy.
     */
    public function getPluralLabel(): string;

    /**
     * Get the WordPress taxonomy arguments.
     * @see https://developer.wordpress.org/reference/functions/register_taxonomy/#arguments
     *
     * @return array The WordPress taxonomy arguments.
     */
    public function getTaxonomyArgs(): array;

    /**
     * Register the taxonomy.
     */
    public function register(): void;
}

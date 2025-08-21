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
     * Get the schema type this taxonomy is associated with.
     *
     * @return string E.g. 'JobPosting', 'Event', 'Project'
     */
    public function getSchemaType(): string;

    /**
     * Get the schema property this taxonomy is associated with.
     * Terms will be created based on this property.
     *
     * @return string E.g. 'relevantOccupation', 'validThrough', 'keywords.name'
     */
    public function getSchemaProperty(): string;

    /**
     * Get object types that this taxonomy applies to.
     *
     * @return string[] E.g. ['post', 'page', 'custom_post_type']
     */
    public function getObjectTypes(): array;

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

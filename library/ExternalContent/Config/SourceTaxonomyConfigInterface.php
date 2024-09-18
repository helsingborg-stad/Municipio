<?php

namespace Municipio\ExternalContent\Config;

interface SourceTaxonomyConfigInterface
{
    /**
     * Get the schemaProperty to use as source for taxonomy terms.
     *
     * @return string
     */
    public function getFromSchemaProperty(): string;

    /**
     * Get the name of the taxonomy.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the singular name of the taxonomy.
     *
     * @return string
     */
    public function getSingularName(): string;
}

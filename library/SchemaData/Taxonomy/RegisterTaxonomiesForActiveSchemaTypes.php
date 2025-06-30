<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFromSchemaType;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFromSchemaTypeInterface;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterTaxonomy;

class RegisterTaxonomiesForActiveSchemaTypes implements Hookable
{
    public function __construct(
        private array $schemaTypes,
        private SchemaToPostTypeResolverInterface $schemaToPostTypeResolver,
        private AddAction&RegisterTaxonomy $wpService,
        private TaxonomiesFromSchemaTypeInterface $taxonomiesFromSchemaType
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerTaxonomies']);
    }

    public function registerTaxonomies(): void
    {
        foreach ($this->schemaTypes as $schemaType) {
            $postTypes  = iterator_to_array($this->schemaToPostTypeResolver->resolve($schemaType));
            $taxonomies = $this->taxonomiesFromSchemaType->create($schemaType);

            foreach ($taxonomies as $taxonomy) {
                $this->wpService->registerTaxonomy($taxonomy->getName(), $postTypes, $taxonomy->getArguments());
            }
        }
    }
}

<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactoryInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterTaxonomy;

/**
 * Class RegisterTaxonomies
 *
 * This class is responsible for registering taxonomies defined in the schema.
 * It uses a factory to create taxonomies and registers them with WordPress.
 */
class RegisterTaxonomies implements Hookable
{
    /**
     * Constructor for the RegisterTaxonomies class.
     */
    public function __construct(
        private TaxonomiesFactoryInterface $taxonomiesFactory,
        private AddAction&RegisterTaxonomy $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerTaxonomies']);
    }

    /**
     * Registers taxonomies defined in the schema.
     *
     * This method retrieves all taxonomies created by the factory and registers them
     * with WordPress using the wpService.
     */
    public function registerTaxonomies(): void
    {
        foreach ($this->taxonomiesFactory->create() as $taxonomy) {
            $this->wpService->registerTaxonomy(
                $taxonomy->getName(),
                $taxonomy->getObjectTypes(),
                $taxonomy->getArguments()
            );
        }
    }
}

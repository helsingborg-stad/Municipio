<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactoryInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterTaxonomy;

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

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerTaxonomies']);
    }

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

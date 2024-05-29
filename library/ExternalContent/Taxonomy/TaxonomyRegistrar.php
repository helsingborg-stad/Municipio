<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Sources\ISourceRegistry;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterTaxonomy;
use WpService\Contracts\RegisterTaxonomyForObjectType;
use WpService\Contracts\TaxonomyExists;

class TaxonomyRegistrar implements ITaxonomyRegistrar, Hookable
{
    /**
     * Class constructor.
     *
     * @param ITaxonomyItem[] $taxonomyItems
     * @param ISourceRegistry $sourceRegistry
     * @param AddAction&RegisterTaxonomy&TaxonomyExists&RegisterTaxonomyForObjectType $wpService
     */
    public function __construct(
        private array $taxonomyItems,
        private ISourceRegistry $sourceRegistry,
        private AddAction&RegisterTaxonomy&TaxonomyExists&RegisterTaxonomyForObjectType $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'register']);
    }

    public function register(): void
    {
        foreach ($this->taxonomyItems as $taxonomyItem) {
            $this->tryRegisterTaxonomy($taxonomyItem);
        }
    }

    private function tryRegisterTaxonomy(ITaxonomyItem $taxonomyItem): void
    {
        foreach ($this->sourceRegistry->getSources() as $source) {
            if ($source->getSchemaObjectType() === $taxonomyItem->getSchemaObjectType()) {
                $this->registerTaxonomy($taxonomyItem, $source->getPostType());
            }
        }
    }

    private function registerTaxonomy(ITaxonomyItem $taxonomyItem, string $postType): void
    {
        if (taxonomy_exists($taxonomyItem->getName())) {
            $this->wpService->registerTaxonomyForObjectType($taxonomyItem->getName(), $postType);
        } else {
            $this->wpService->registerTaxonomy($taxonomyItem->getName(), $postType, $taxonomyItem->getTaxonomyArgs());
        }
    }
}

<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Sources\SourceRegistryInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterTaxonomy;
use WpService\Contracts\RegisterTaxonomyForObjectType;
use WpService\Contracts\TaxonomyExists;

class TaxonomyRegistrar implements TaxonomyRegistrarInterface
{
    private array $registeredTaxonomyItems = [];

    /**
     * Class constructor.
     *
     * @param TaxonomyItemInterface[] $taxonomyItems
     * @param SourceRegistryInterface $sourceRegistry
     * @param AddAction&RegisterTaxonomy&TaxonomyExists&RegisterTaxonomyForObjectType $wpService
     */
    public function __construct(
        private array $taxonomyItems,
        private SourceRegistryInterface $sourceRegistry,
        private AddAction&RegisterTaxonomy&TaxonomyExists&RegisterTaxonomyForObjectType $wpService
    ) {
    }

    public function register(): void
    {
        foreach ($this->taxonomyItems as $taxonomyItem) {
            $this->tryRegisterTaxonomy($taxonomyItem);
        }
    }

    public function getRegisteredTaxonomyItems(): array
    {
        return $this->registeredTaxonomyItems;
    }

    private function tryRegisterTaxonomy(TaxonomyItemInterface $taxonomyItem): void
    {
        foreach ($this->sourceRegistry->getSources() as $source) {
            if ($source->getSchemaObjectType() === $taxonomyItem->getSchemaObjectType()) {
                $this->registerTaxonomy($taxonomyItem, $source->getPostType());
            }
        }
    }

    private function registerTaxonomy(TaxonomyItemInterface $taxonomyItem, string $postType): void
    {
        if (taxonomy_exists($taxonomyItem->getName())) {
            $this->wpService->registerTaxonomyForObjectType($taxonomyItem->getName(), $postType);
        } else {
            $this->wpService->registerTaxonomy($taxonomyItem->getName(), $postType, $taxonomyItem->getTaxonomyArgs());
            $this->registeredTaxonomyItems[] = $taxonomyItem;
        }
    }
}

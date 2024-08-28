<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Sources\SourceRegistryInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterTaxonomy;
use WpService\Contracts\RegisterTaxonomyForObjectType;
use WpService\Contracts\TaxonomyExists;

class TaxonomyRegistrar implements TaxonomyRegistrarInterface, Hookable
{
    private array $registeredTaxonomyItems = [];

    /**
     * Class constructor.
     *
     * @param SourceRegistryInterface $sourceRegistry
     * @param AddAction&RegisterTaxonomy&TaxonomyExists&RegisterTaxonomyForObjectType $wpService
     */
    public function __construct(
        private SourceRegistryInterface $sourceRegistry,
        private TaxonomyItemsFactoryInterface $taxonomyItemsFactory,
        private AddAction&RegisterTaxonomy&TaxonomyExists&RegisterTaxonomyForObjectType $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'registerTaxonomies'));
    }

    public function registerTaxonomies(): void
    {
        foreach ($this->sourceRegistry->getSources() as $source) {
            foreach ($this->taxonomyItemsFactory->create($source) as $taxonomyItem) {
                $this->register($taxonomyItem);
            }
        }
    }

    public function register(TaxonomyItem $taxonomyItem): void
    {
        $this->tryRegisterTaxonomy($taxonomyItem);
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

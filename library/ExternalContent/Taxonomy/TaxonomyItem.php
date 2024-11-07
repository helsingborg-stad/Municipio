<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Config\SourceTaxonomyConfigInterface;
use WpService\Contracts\__;
use WpService\Contracts\RegisterTaxonomy;

/**
 * Class TaxonomyItem
 *
 * This class represents a taxonomy item and provides methods to interact with it.
 */
class TaxonomyItem implements TaxonomyItemInterface
{
    /**
     * Class constructor.
     */
    public function __construct(
        private string $schemaObjectType,
        private array $postTypes,
        private SourceTaxonomyConfigInterface $taxonomyConfig,
        private __&RegisterTaxonomy $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getSchemaObjectType(): string
    {
        return $this->schemaObjectType;
    }

    /**
     * @inheritDoc
     */
    public function getPostTypes(): array
    {
        return $this->postTypes;
    }

    /**
     * @inheritDoc
     */
    public function getSchemaObjectProperty(): string
    {
        return $this->taxonomyConfig->getFromSchemaProperty();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        // Prepend the schema object type to the schema object property.
        $name = $this->schemaObjectType . '_' . $this->getSchemaObjectProperty();

        // Replace . with underscore in the schema object property.
        $name = str_replace('.', '_', $name);

        // Remove special characters from the schema object property. Allow underscores.
        $name = preg_replace(
            '/[^a-zA-Z0-9_]/',
            '',
            $name
        );

        // Convert the schema object property to a taxonomy name by making it snake case from camelcase.
        $name = strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0', $name)
        );

        // Ensure that the taxonomy name is not longer than 32 characters.
        return substr($name, 0, 32);
    }

    /**
     * @inheritDoc
     */
    public function getSingleLabel(): string
    {
        return $this->taxonomyConfig->getSingularName();
    }

    /**
     * @inheritDoc
     */
    public function getPluralLabel(): string
    {
        return $this->taxonomyConfig->getName();
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomyArgs(): array
    {
        return [
            'labels'            => $this->getLabels(),
            'hierarchical'      => $this->taxonomyConfig->isHierarchical(),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => $this->getName()],
        ];
    }

    /**
     * Register the taxonomy with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        $this->wpService->registerTaxonomy($this->getName(), $this->getPostTypes(), $this->getTaxonomyArgs());
    }

    /**
     * Get the labels for the taxonomy.
     *
     * @return array The taxonomy labels.
     */
    private function getLabels(): array
    {
        return [
            'name'                       => $this->taxonomyConfig->getName(),
            'singular_name'              => $this->taxonomyConfig->getSingularName(),
            'search_items'               => sprintf($this->wpService->__('Search %s'), $this->taxonomyConfig->getName()),
            'popular_items'              => sprintf($this->wpService->__('Popular %s'), $this->taxonomyConfig->getName()),
            'all_items'                  => sprintf($this->wpService->__('All %s'), $this->taxonomyConfig->getName()),
            'parent_item'                => sprintf($this->wpService->__('Parent %s'), $this->taxonomyConfig->getSingularName()),
            'parent_item_colon'          => sprintf($this->wpService->__('Parent %s:'), $this->taxonomyConfig->getSingularName()),
            'edit_item'                  => sprintf($this->wpService->__('Edit %s'), $this->taxonomyConfig->getSingularName()),
            'view_item'                  => sprintf($this->wpService->__('View %s'), $this->taxonomyConfig->getSingularName()),
            'update_item'                => sprintf($this->wpService->__('Update %s'), $this->taxonomyConfig->getSingularName()),
            'add_new_item'               => sprintf($this->wpService->__('Add New %s'), $this->taxonomyConfig->getSingularName()),
            'new_item_name'              => sprintf($this->wpService->__('New %s Name'), $this->taxonomyConfig->getSingularName()),
            'separate_items_with_commas' => sprintf($this->wpService->__(
                'Separate %s with commas'
            ), $this->taxonomyConfig->getName()),
            'add_or_remove_items'        => sprintf($this->wpService->__('Add or remove %s'), $this->taxonomyConfig->getName()),
            'choose_from_most_used'      => sprintf($this->wpService->__(
                'Choose from the most used %s'
            ), $this->taxonomyConfig->getName()),
            'not_found'                  => sprintf($this->wpService->__('No %s found.'), $this->taxonomyConfig->getName()),
            'menu_name'                  => $this->taxonomyConfig->getName(),
        ];
    }
}

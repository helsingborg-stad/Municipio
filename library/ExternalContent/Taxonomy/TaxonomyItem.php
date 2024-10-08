<?php

namespace Municipio\ExternalContent\Taxonomy;

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
        private string $schemaObjectProperty,
        private string $singleLabel,
        private string $pluralLabel,
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
        return $this->schemaObjectProperty;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        // Prepend the schema object type to the schema object property.
        $name = $this->schemaObjectType . '_' . $this->schemaObjectProperty;

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
        return $this->singleLabel;
    }

    /**
     * @inheritDoc
     */
    public function getPluralLabel(): string
    {
        return $this->pluralLabel;
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomyArgs(): array
    {
        return [
            'labels'            => $this->getLabels(),
            'hierarchical'      => false,
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
            'name'                       => $this->pluralLabel,
            'singular_name'              => $this->singleLabel,
            'search_items'               => sprintf($this->wpService->__('Search %s'), $this->pluralLabel),
            'popular_items'              => sprintf($this->wpService->__('Popular %s'), $this->pluralLabel),
            'all_items'                  => sprintf($this->wpService->__('All %s'), $this->pluralLabel),
            'parent_item'                => sprintf($this->wpService->__('Parent %s'), $this->singleLabel),
            'parent_item_colon'          => sprintf($this->wpService->__('Parent %s:'), $this->singleLabel),
            'edit_item'                  => sprintf($this->wpService->__('Edit %s'), $this->singleLabel),
            'view_item'                  => sprintf($this->wpService->__('View %s'), $this->singleLabel),
            'update_item'                => sprintf($this->wpService->__('Update %s'), $this->singleLabel),
            'add_new_item'               => sprintf($this->wpService->__('Add New %s'), $this->singleLabel),
            'new_item_name'              => sprintf($this->wpService->__('New %s Name'), $this->singleLabel),
            'separate_items_with_commas' => sprintf($this->wpService->__(
                'Separate %s with commas'
            ), $this->pluralLabel),
            'add_or_remove_items'        => sprintf($this->wpService->__('Add or remove %s'), $this->pluralLabel),
            'choose_from_most_used'      => sprintf($this->wpService->__(
                'Choose from the most used %s'
            ), $this->pluralLabel),
            'not_found'                  => sprintf($this->wpService->__('No %s found.'), $this->pluralLabel),
            'menu_name'                  => $this->pluralLabel,
        ];
    }
}

<?php

namespace Municipio\PostsList\ViewCallableProviders\Table\TableArguments;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetTaxonomies;
use WpService\Contracts\__;

/**
 * Generates table headings based on the appearance configuration
 */
class TableHeadingsGenerator
{
    /**
     * Constructor
     */
    public function __construct(
        private AppearanceConfigInterface $appearanceConfig,
        private array $posts,
        private GetPostTypeObject&GetTaxonomies&__ $wpService
    ) {
    }

    /**
     * Generate table headings
     *
     * @return array
     */
    public function generate(): array
    {
        $headings = array_map(
            fn($item) => $this->getHeadingLabel($item),
            $this->appearanceConfig->getPostPropertiesToDisplay()
        );
        foreach ($this->getTaxonomyHeadings() as $taxonomyHeading) {
            $headings[] = $taxonomyHeading;
        }
        return $headings;
    }

    /**
     * Get the label for a specific heading based on the item type
     *
     * @param string $item
     * @return string
     */
    private function getHeadingLabel(string $item): string
    {
        return match ($item) {
            'post_title' => $this->getPostTypeSingularName($this->posts[0]->getPostType() ?? '') ?? $this->wpService->__('Title', 'municipio'),
            'post_date'  => $this->wpService->__('Published', 'municipio'),
            default      => ucfirst(str_replace('_', ' ', $item)),
        };
    }

    /**
     * Get taxonomy headings based on the appearance configuration
     *
     * @return array
     */
    private function getTaxonomyHeadings(): array
    {
        $taxonomies = $this->appearanceConfig->getTaxonomiesToDisplay();
        if (empty($taxonomies)) {
            return [];
        }

        $allTaxonomies = $this->wpService->getTaxonomies([], 'objects');

        return array_map(
            fn($taxonomy) => $allTaxonomies[$taxonomy]->labels->singular_name ?? ucfirst($taxonomy),
            $taxonomies
        );
    }

    /**
     * Get the singular name of a post type when only one post type is present.
     * @return string|null
     */
    private function getPostTypeSingularName(): ?string
    {
        // Check if only one post type is present
        $postTypes = array_unique(array_map(fn($post) => $post->getPostType(), $this->posts));

        if (count($postTypes) !== 1) {
            return null;
        }

        $postTypeObject = $this->wpService->getPostTypeObject(reset($postTypes));
        return $postTypeObject->labels->singular_name ?? null;
    }
}

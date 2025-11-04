<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use WpService\Contracts\WpDate;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetTerms;

/**
 * Generates table items for posts based on the appearance configuration
 */
class TableItemsGenerator
{
    /**
     * Constructor
     */
    public function __construct(
        private AppearanceConfigInterface $appearanceConfig,
        private array $posts,
        private WpDate&GetOption&GetTerms $wpService,
        private TaxonomyTermsProviderInterface $termsProvider,
        private LabelFormatterInterface $labelFormatter
    ) {
    }

    /**
     * Generate table items for the posts
     *
     * @return array
     */
    public function generate(): array
    {
        if (empty($this->posts)) {
            return [];
        }
        return array_map(fn($post) => $this->getItem($post), $this->posts);
    }

    /**
     * Get a single table item for a post
     *
     * @param PostObjectInterface $post
     * @return array
     */
    private function getItem(PostObjectInterface $post): array
    {
        $columns         = array_map(
            fn($item) => $this->getColumnValue($post, $item),
            $this->appearanceConfig->getPostPropertiesToDisplay()
        );
        $taxonomyColumns = $this->getTaxonomyColumns($post);
        return [
            'id'      => $post->getId(),
            'href'    => $post->getPermalink(),
            'columns' => [...$columns, ...$taxonomyColumns],
        ];
    }

    /**
     * Get the value for a specific column based on the item type
     *
     * @param PostObjectInterface $post
     * @param string $item
     * @return string
     */
    private function getColumnValue(PostObjectInterface $post, string $item): string
    {
        return match ($item) {
            'post_title' => $post->getTitle(),
            'post_date'  => $this->wpService->wpDate(
                $this->wpService->getOption('date_format'),
                $post->getPublishedTime()
            ),
            default      => $post->{$item} ?? '',
        };
    }

    /**
     * Get taxonomy column values for a post
     *
     * @param PostObjectInterface $post
     * @return array
     */
    private function getTaxonomyColumns(PostObjectInterface $post): array
    {
        $taxonomyColumns = [];
        foreach ($this->appearanceConfig->getTaxonomiesToDisplay() ?? [] as $taxonomy) {
            $taxonomyColumns[$taxonomy] = $this->getTaxonomyColumnValue($post, $taxonomy);
        }
        return $taxonomyColumns;
    }

    /**
     * Get taxonomy column values for a post
     *
     * @param PostObjectInterface $post
     * @param string $taxonomy
     * @return string
     */
    private function getTaxonomyColumnValue(PostObjectInterface $post, string $taxonomy): string
    {
        $terms = array_filter($this->termsProvider->getAllTerms(), fn($term) => $post->getId() === $term->object_id && $term->taxonomy === $taxonomy);
        if (empty($terms)) {
            return '';
        }
        $termNames = array_map(fn($term) => $this->labelFormatter->formatTermName($term->name ?? ''), $terms);
        return join(', ', $termNames);
    }
}

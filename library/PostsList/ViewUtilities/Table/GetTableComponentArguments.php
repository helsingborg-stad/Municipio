<?php

namespace Municipio\PostsList\ViewUtilities\Table;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;
use Municipio\Helper\Memoize\MemoizedFunction;
use WP_Error;
use WP_Term;
use WpService\Contracts\__;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetTaxonomies;
use WpService\Contracts\GetTerms;
use WpService\Contracts\GetTheTerms;
use WpService\Contracts\WpDate;

class GetTableComponentArguments implements ViewUtilityInterface
{
    private MemoizedFunction $getAllTermsByTaxonomyMemoized;

    /**
     * Constructor
     *
     * @param PostObjectInterface[] $posts
     * @param AppearanceConfigInterface $appearanceConfig
     */
    public function __construct(
        private array $posts,
        private AppearanceConfigInterface $appearanceConfig,
        private GetPostTypeObject&GetTaxonomies&WpDate&GetTheTerms&GetOption&__&GetTerms $wpService
    ) {
        $this->getAllTermsByTaxonomyMemoized = new MemoizedFunction(
            fn() => $this->computeAllTerms()
        );
    }

    public function getCallable(): callable
    {
        return fn() => $this->getTableArguments();
    }

    private function getTableArguments(): array
    {
        return [
            'headings' => $this->getHeadings(),
            'list'     => $this->getItems(),
        ];
    }

    private function getHeadings(): array
    {
        if (empty($this->appearanceConfig->getPostPropertiesToDisplay())) {
            return [];
        }

        $headings = array_map(
            fn($item) => $this->getHeadingLabel($item),
            $this->appearanceConfig->getPostPropertiesToDisplay()
        );

        foreach ($this->getTaxonomyHeadings() as $taxonomyHeading) {
            $headings[] = $taxonomyHeading;
        }

        return $headings;
    }

    private function getHeadingLabel(string $item): string
    {
        return match ($item) {
            'post_title' => $this->getPostTypeSingularName($this->posts[0]->getPostType() ?? '') ?? $this->wpService->__('Title', 'municipio'),
            'post_date'  => $this->wpService->__('Published', 'municipio'),
            default      => ucfirst(str_replace('_', ' ', $item)),
        };
    }

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

    private function getItems(): array
    {
        if (empty($this->posts)) {
            return [];
        }

        return array_map(fn($post) => $this->getItem($post), $this->posts);
    }

    private function getItem(PostObjectInterface $post): array
    {
        $columns = array_map(
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

    private function getColumnValue(PostObjectInterface $post, string $item): string
    {
        return match ($item) {
            'post_title' => $post->getTitle(),
            'post_date'  => $this->wpService->wpDate(
                $this->wpService->getOption('date_format'),
                strtotime($post->getPublishedTime())
            ),
            default      => $post->{$item} ?? '',
        };
    }

    private function getTaxonomyColumns(PostObjectInterface $post): array
    {
        $taxonomyColumns = [];
        foreach ($this->appearanceConfig->getTaxonomiesToDisplay() ?? [] as $taxonomy) {
            $taxonomyColumns[$taxonomy] = $this->getTaxonomyColumnValue($post, $taxonomy);
        }
        return $taxonomyColumns;
    }

    private function getTaxonomyColumnValue(PostObjectInterface $post, string $taxonomy): string
    {
        $terms = array_filter($this->getAllTerms(), fn($term) => $post->getId() === $term->object_id && $term->taxonomy === $taxonomy);

        if (empty($terms)) {
            return '';
        }

        $termNames = array_map(fn($term) => $this->formatTermName($term->name ?? ''), $terms);

        return join(', ', $termNames);
    }

    /**
     * Memoized all terms by taxonomy using MemoizedFunction
     *
     * @return WP_Term[]
     */
    private function getAllTerms(): array
    {
        return ($this->getAllTermsByTaxonomyMemoized)();
    }

    /**
     * Computes all terms
     *
     * @return WP_Term[]
     */
    private function computeAllTerms(): array
    {
        if (empty($this->appearanceConfig->getTaxonomiesToDisplay())) {
            return [];
        }

        return $this->wpService->getTerms([
            'taxonomy'   => $this->appearanceConfig->getTaxonomiesToDisplay(),
            'hide_empty' => false,
            'object_ids' => array_map(fn($post) => $post->getId(), $this->posts),
            'fields'     => 'all_with_object_id',
        ]);
    }

    private function formatTermName(string $name): string
    {
        $name         = trim($name);
        $datePatterns = [
            '/^\d{4}-\d{2}-\d{2}$/',         // YYYY-MM-DD
            '/^\d{2}\/\d{2}\/\d{4}$/',       // DD/MM/YYYY or MM/DD/YYYY
            '/^\d{2}-\d{2}-\d{4}$/',         // DD-MM-YYYY
            '/^\w+ \d{4}$/',                 // "Month YYYY"
            '/^\d{1,2} \p{L}+, \d{4}$/u',    // "30 january, 2025"
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', // "2025-11-09T00:00:00+00:00"
        ];

        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $name)) {
                $timestamp = strtotime($name);
                if ($timestamp && $timestamp > 0) {
                    return $this->wpService->wpDate($this->wpService->getOption('date_format'), $timestamp);
                }
            }
        }

        return $name;
    }

    private function getPostTypeSingularName(string $postType): ?string
    {
        $postTypeObject = $this->wpService->getPostTypeObject($postType);
        return $postTypeObject->labels->singular_name ?? null;
    }
}

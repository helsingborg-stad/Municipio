<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use WpService\Contracts\WpDate;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetTerms;

class TableItemsGenerator
{
    public function __construct(
        private AppearanceConfigInterface $appearanceConfig,
        private array $posts,
        private WpDate&GetOption&GetTerms $wpService,
        private TaxonomyTermsProviderInterface $termsProvider,
        private LabelFormatterInterface $labelFormatter
    ) {
    }

    public function generate(): array
    {
        if (empty($this->posts)) {
            return [];
        }
        return array_map(fn($post) => $this->getItem($post), $this->posts);
    }

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
        $terms = array_filter($this->termsProvider->getAllTerms(), fn($term) => $post->getId() === $term->object_id && $term->taxonomy === $taxonomy);
        if (empty($terms)) {
            return '';
        }
        $termNames = array_map(fn($term) => $this->labelFormatter->formatTermName($term->name ?? ''), $terms);
        return join(', ', $termNames);
    }
}

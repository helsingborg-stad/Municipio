<?php

namespace Municipio\PostsList\ViewUtilities\Table;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;
use WP_Error;
use WpService\Contracts\__;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetTaxonomies;
use WpService\Contracts\GetTheTerms;
use WpService\Contracts\WpDate;

class GetTableComponentArguments implements ViewUtilityInterface
{
    /**
     * Constructor
     *
     * @param PostObjectInterface[] $posts
     * @param AppearanceConfigInterface $appearanceConfig
     */
    public function __construct(
        private array $posts,
        private AppearanceConfigInterface $appearanceConfig,
        private GetPostTypeObject&GetTaxonomies&WpDate&GetTheTerms&GetOption&__ $wpService
    ) {
    }

    public function getCallable(): callable
    {
        return fn() => $this->getHeadings();
    }

    private function getHeadings(): array
    {
        $items    = [];
        $headings = array_map(
            function ($item) {
                return match ($item) {
                    'post_title' =>  $this->getPostTypeSingularName($this->posts[0]->getPostType()) ?? $this->wpService->__('Title', 'municipio'),
                    'post_date' => $this->wpService->__('Published', 'municipio'),
                    default => ucfirst(str_replace('_', ' ', $item)),
                };
            },
            $this->appearanceConfig->getPostPropertiesToDisplay()
        );

        if (!empty($this->appearanceConfig->getTaxonomiesToDisplay())) {
            $allTaxonomies = $this->wpService->getTaxonomies([], 'objects');
            foreach ($this->appearanceConfig->getTaxonomiesToDisplay() as $taxonomy) {
                $headings[] = $allTaxonomies[$taxonomy]->labels->singular_name;
            }
        }

        if (!empty($this->posts)) {
            foreach ($this->posts as $post) {
                $columns = array_map(fn($item) => match ($item) {
                            'post_title' => $post->getTitle(),
                            'post_date' => $this->wpService->wpDate($this->wpService->getOption('date_format'), strtotime($post->getPublishedTime())),
                            default => $post->{$item} ?? ''
                }, $this->appearanceConfig->getPostPropertiesToDisplay());

                $columns = [...$columns, ...$this->getTaxonomyColumns($post)];

                $items[] =
                    [
                        'id'      => $post->getId(),
                        'href'    => $post->getPermalink(),
                        'columns' => $columns,
                    ];
            }
        }

        return [
            'headings' => $headings,
            'list'     => $items,
        ];
    }

    private function getTaxonomyColumns(PostObjectInterface $post): array
    {
        $taxonomyColumns = [];
        if (!empty($this->appearanceConfig->getTaxonomiesToDisplay())) {
            foreach ($this->appearanceConfig->getTaxonomiesToDisplay() as $taxonomy) {
                $terms = $this->wpService->getTheTerms($post->getId(), $taxonomy);

                if (is_a($terms, WP_Error::class) || empty($terms)) {
                    $taxonomyColumns[$taxonomy] = '';
                    continue;
                }

                $termNames = array_map(function ($term) {
                    $name = trim($term->name ?? '');

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

                            // Ensure strtotime() returned a valid timestamp
                            if ($timestamp && $timestamp > 0) {
                                return $this->wpService->wpDate($this->wpService->getOption('date_format'), $timestamp);
                            }
                        }
                    }

                    return $name;
                }, $terms);

                $taxonomyColumns[$taxonomy] = join(', ', $termNames);
            }
        }

        return $taxonomyColumns;
    }

    private function getPostTypeSingularName(string $postType): ?string
    {
        $postTypeObject = $this->wpService->getPostTypeObject($postType);
        if ($postTypeObject && !empty($postTypeObject->labels->singular_name)) {
            return $postTypeObject->labels->singular_name;
        }

        return null;
    }
}

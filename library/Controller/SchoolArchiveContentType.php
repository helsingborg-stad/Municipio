<?php

namespace Municipio\Controller;

use Municipio\Helper\WP;

/**
 * Class SchoolArchiveContentType
 *
 * @package Municipio\Controller
 */
class SchoolArchiveContentType extends \Municipio\Controller\ArchiveContentType
{
    /**
     * Initialize the SchoolArchiveContentType.
     */
    public function init()
    {
        parent::init();
        $this->addHooks();
    }

    /**
     * Add hooks for the SchoolArchiveContentType.
     */
    private function addHooks()
    {
        if ($this->shouldDisplayAsList()) {
            add_filter('Municipio/Controller/Archive/Data', [$this, 'modifyArchiveListAppearance']);
        }
    }

    /**
     * Modify the appearance of the archive list.
     *
     * @param array $data The archive data.
     * @return array The modified archive data.
     */
    public function modifyArchiveListAppearance($data)
    {
        if (!is_array($data['posts']['items'])) {
            return $data;
        }

        $posts          = $data['posts']['items'];
        $taxonomies     = array_filter(['school-area'], 'taxonomy_exists');
        $postType       = get_post_type();
        $postTypeObject = get_post_type_object($postType);
        $postTypeLabel  = $postTypeObject->label;

        $headings = array_map(function ($taxonomy) {
            return get_taxonomy_labels(get_taxonomy($taxonomy))->name;
        }, $taxonomies);

        array_unshift($headings, $postTypeLabel);

        $preparedPosts = [
            'items'    => [],
            'headings' => $headings
        ];

        foreach ($posts as $post) {
            $postTitle                = $post['columns'][0];
            $termNames                = array_map(
                fn ($taxonomy) => $this->getTermName($taxonomy, $post['id']),
                $taxonomies
            );
            $title                    = sprintf(
                '<a href="%s" title="%s">%s</a>',
                $post['href'],
                $postTitle,
                $postTitle
            );
            $columns                  = array_merge([$title], $termNames);
            $preparedPosts['items'][] = ['columns' => $columns];
        }

        $data['posts'] = $preparedPosts;

        return $data;
    }

    /**
     * Get the term name for a given taxonomy and post ID.
     *
     * @param string $taxonomy The taxonomy.
     * @param int $postId The post ID.
     * @return string The term name.
     */
    private function getTermName(string $taxonomy, int $postId): string
    {
        $terms = wp_get_object_terms($postId, $taxonomy);
        return !empty($terms) ? $terms[0]->name : '';
    }

    /**
     * Check if the archive should be displayed as a list.
     *
     * @return bool True if the archive should be displayed as a list, false otherwise.
     */
    private function shouldDisplayAsList(): bool
    {
        return isset($this->data['archiveProps']) && $this->data['archiveProps']->style === 'list';
    }
}

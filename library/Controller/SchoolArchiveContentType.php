<?php

namespace Municipio\Controller;

use Municipio\Helper\WP;

class SchoolArchiveContentType extends \Municipio\Controller\ArchiveContentType
{
    public function init()
    {
        parent::init();
        $this->addHooks();
    }

    private function addHooks()
    {
        if ($this->shouldDisplayAsList()) {
            add_filter('Municipio/Controller/Archive/Data', [$this, 'modifyArchiveListAppearance']);
        }
    }

    public function modifyArchiveListAppearance($data)
    {
        if (!is_array($data['posts']['items'])) {
            return $data;
        }

        $posts = $data['posts']['items'];
        $taxonomies = array_filter(['school-area'], 'taxonomy_exists');
        $postType = get_post_type();
        $postTypeObject = get_post_type_object($postType);
        $postTypeLabel = $postTypeObject->label;

        $headings = array_map(function ($taxonomy) {
            return get_taxonomy_labels(get_taxonomy($taxonomy))->name;
        }, $taxonomies);

        array_unshift($headings, $postTypeLabel);

        $preparedPosts = [
            'items'    => [],
            'headings' => $headings
        ];

        foreach ($posts as $post) {

            $postTitle = $post['columns'][0];
            $termNames = array_map(fn ($taxonomy) => $this->getTermName($taxonomy, $post['id']), $taxonomies);
            $title = sprintf('<a href="%s" title="%s">%s</a>', $post['href'], $postTitle, $postTitle);
            $columns = array_merge([$title], $termNames);
            $preparedPosts['items'][] = ['columns' => $columns];
        }

        $data['posts'] = $preparedPosts;

        return $data;
    }

    private function getTermName(string $taxonomy, int $postId): string
    {
        $terms = get_terms(['object_ids' => [$postId], 'taxonomy' => $taxonomy]);
        return !empty($terms) ? $terms[0]->name : '';
    }

    private function shouldDisplayAsList(): bool
    {
        return isset($this->data['archiveProps']) && $this->data['archiveProps']->style === 'list';
    }
}

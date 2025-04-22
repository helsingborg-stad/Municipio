<?php

namespace Municipio\Controller;

use Municipio\PostObject\PostObjectInterface;

/**
 * Class ArchiveProject
 *
 * Handles archive for posts using the Project schema type.
 */
class ArchiveProject extends \Municipio\Controller\Archive
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->data['getTermsList'] = fn($post, $taxonomy) => $this->getTermsList($post, $taxonomy);
    }

    /**
     * Gets a list of terms for a given taxonomy and post.
     *
     * @param PostObjectInterface $post
     * @param string $taxonomy
     * @return string
     */
    private function getTermsList(PostObjectInterface $post, string $taxonomy): string
    {
        $terms = $post->getTerms([$taxonomy]);

        if (empty($terms)) {
            return '';
        }

        return implode(' / ', array_map(fn($term) => $term->name, $terms));
    }
}

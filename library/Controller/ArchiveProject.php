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

        $this->data['getTermsList'] = fn($post, $taxonomy, $separator) => $this->getTermsList($post, $taxonomy, $separator);
    }

    /**
     * Gets a list of terms for a given taxonomy and post.
     *
     * @param PostObjectInterface $post
     * @param string $taxonomy
     * @param string $separator
     * @return string
     */
    private function getTermsList(PostObjectInterface $post, string $taxonomy, string $separator): string
    {
        $terms = $post->getTerms([$taxonomy]);

        if (empty($terms)) {
            return '';
        }

        return implode($separator, array_map(fn($term) => $term->name, $terms));
    }
}

<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\Helper\Sanitize;
use Municipio\PostObject\PostObjectInterface;

/*
 * View utility to get excerpt without links
 */
class GetExcerptWithoutLinks implements ViewCallableProviderInterface
{
    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post): string {
            $post->post_excerpt = $post->getExcerpt();
            $post->post_content = $post->getContent();
            [$excerptContent, $hasExcerpt] = \Municipio\Helper\Post::getPostExcerpt($post);

            return wp_trim_words(Sanitize::sanitizeATags($excerptContent), 30, '...');
        };
    }
}

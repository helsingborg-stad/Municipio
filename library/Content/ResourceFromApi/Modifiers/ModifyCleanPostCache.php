<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

/**
 * Class ModifyCleanPostCache
 */
class ModifyCleanPostCache
{
    /**
     * Handle the post cache cleaning.
     *
     * @param int $postId The ID of the post.
     * @param object $post The post object.
     */
    public function handle($postId, $post)
    {
        wp_cache_delete($post->name, $post->post_type . '-posts');
    }
}

<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

class ModifyCleanPostCache
{
    public function handle($postId, $post)
    {
        wp_cache_delete($post->name, $post->post_type . '-posts');
    }
}

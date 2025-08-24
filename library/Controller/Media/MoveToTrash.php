<?php

namespace Municipio\Controller\Media;

use Municipio\HooksRegistrar\Hookable;

use WpService\Contracts\{
    AddFilter,
    WpTrashPost
};

class MoveToTrash implements Hookable 
{
    public function __construct(private AddFilter&WpTrashPost $wpService) 
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('pre_delete_attachment', array($this, 'moveMediaToTrash'), 10, 3);
    }

    public function moveMediaToTrash($delete, $post, $forceDelete)
    {
        if ($forceDelete) {
            return $delete;
        }

        if ($post->post_status === 'trash') {
            return $delete; // Already in trash, no need to process
        }

        $this->wpService->wpTrashPost($post->ID);
        return true;
    }
}
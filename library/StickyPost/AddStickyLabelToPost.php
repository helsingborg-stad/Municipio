<?php

namespace Municipio\StickyPost;

use Municipio\HooksRegistrar\Hookable;
use Municipio\StickyPost\Helper\GetStickyOption;
use WpService\Contracts\__;
use WpService\Contracts\AddFilter;

class AddStickyLabelToPost implements Hookable
{
    public function __construct(
        private GetStickyOption $getStickyOption,
        private AddFilter&__ $wpService
    )
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('display_post_states', array($this, 'addStickyLabel'), 10, 2);
    }

    public function addStickyLabel($postStates, $post)
    {
        if (empty($post) || empty($post->post_type)) {
            return $postStates;
        }

        if (array_key_exists($post->ID, $this->getStickyOption->getOption($post->post_type))) {
            $postStates['sticky'] = $this->wpService->__('Sticky', 'municipio');
        }

        return $postStates;
    }
}
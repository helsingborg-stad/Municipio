<?php

namespace Municipio\StickyPost;

use Municipio\HooksRegistrar\Hookable;
use Municipio\StickyPost\Helper\GetStickyOption;
use WpService\Contracts\__;
use WpService\Contracts\AddFilter;

/**
 * Represents a AddStickyLabelToPost class.
 */
class AddStickyLabelToPost implements Hookable
{
    /**
     * Constructor for the AddStickyLabelToPost class.
     */
    public function __construct(
        private GetStickyOption $getStickyOption,
        private AddFilter&__ $wpService
    ) {
    }

    /**
     * Adds hooks for the AddStickyLabelToPost class.
     *
     * This method adds hooks to display the sticky label for both posts and attachments.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('display_post_states', array($this, 'addStickyLabel'), 10, 2);
    }

    /**
     * Adds the sticky label to the post states.
     *
     * @param array $postStates The post states.
     * @param object $post The post object.
     * @return array The post states.
     */
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

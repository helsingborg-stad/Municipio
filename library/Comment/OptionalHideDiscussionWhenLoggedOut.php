<?php

namespace Municipio\Comment;

use AcfService\Contracts\GetField;
use Municipio\HooksRegistrar\Hookable;
use WP_Comment_Query;
use WpService\Contracts\{AddAction,IsUserLoggedIn};

/**
 * Hide discussion from logged out users.
 * Check if option is set to hide discussion from logged out users and hide discussion if user is not logged in.
 * Runs on the `pre_get_comments` action.
 */
class OptionalHideDiscussionWhenLoggedOut implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private AddAction&IsUserLoggedIn $wpService, private GetField $acfService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('pre_get_comments', [$this, 'hideDiscussionFromLoggedOutUser'], 10, 1);
    }

    /**
     * Hide discussion for logged out users if option is set.
     *
     * @param WP_Comment_Query $query
     */
    public function hideDiscussionFromLoggedOutUser(WP_Comment_Query $query): void
    {
        $hideForLoggedOutUsers = $this->acfService->getField('hide_discussion_for_logged_out_users', 'option');

        if (!$this->wpService->isUserLoggedIn() && $hideForLoggedOutUsers === true) {
            $query = $this->getDisabledCommentQuery($query);
        }
    }

    /**
     * Disable comments by setting post__in to an empty array.
     * This will make the query return no comments.
     *
     * @param WP_Comment_Query $query
     * @return WP_Comment_Query
     */
    private function getDisabledCommentQuery(WP_Comment_Query $query): WP_Comment_Query
    {
        $query->query_vars['post__in'] = [0];
        return $query;
    }
}

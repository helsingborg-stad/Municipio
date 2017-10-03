<?php

namespace Intranet\Search;

class General
{
    public static function jsonSearch($data)
    {
        $q = sanitize_text_field($data['s']);

        $postStatuses  = array('publish', 'inherit');

        if (is_user_logged_in()) {
            $postStatuses[] = 'private';
        }

        $query = new \WP_Query(array(
            's'             => $q,
            'orderby'       => 'relevance',
            'sites'         => 'all',
            'post_status'   => $postStatuses,
            'post_type'     => \Intranet\Helper\PostType::getPublic(),
            'cache_results' => true,
            'posts_per_page' => 5
        ));

        $users = array();
        if (is_user_logged_in()) {
            $users = \Intranet\User\General::searchUsers($q, 5);
        }

        return array(
            'content' => $query->posts,
            'users' => $users
        );
    }
}

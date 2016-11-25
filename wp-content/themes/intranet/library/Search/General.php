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
            'cache_results' => false
        ));

        $users = array();
        if (is_user_logged_in()) {
            $users = \Intranet\User\General::searchUsers($q);
        }

        return array(
            'content' => array_slice($query->posts, 0, 5),
            'users' => array_slice($users, 0, 5)
        );
    }
}

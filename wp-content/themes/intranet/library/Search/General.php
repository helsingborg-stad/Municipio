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

        if ($cache = $this->getCachedResponse($this->createCacheHash(array($postStatuses, $q)))) {
            return $cache;
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

        $response =  array(
            'content' => $query->posts,
            'users' => $users
        );

        $this->setCachedResponse($response);

        return $response;
    }

    private function getCachedResponse($hash)
    {
        return wp_cache_get('json_search_query', $hash);
    }

    private function setCachedResponse($response, $hash)
    {
        return wp_cache_set('json_search_query', $response, $hash, 60*60);
    }

    private function createCacheHash($args)
    {
        return md5(maybe_serialize($args));
    }
}

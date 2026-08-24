<?php

declare(strict_types=1);


namespace Municipio\Search\Index\Helper;

class Id
{
    /**
     * Get the id
     *
     * @return string
     */
    public static function getId($postId): string
    {
        if (is_multisite()) {
            return (
                str_replace('.', '-', parse_url(network_site_url())['host'])
                . '-'
                . get_current_blog_id()
                . '-'
                . $postId
            );
        }
        return str_replace('.', '-', parse_url(home_url())['host']) . '-0-' . $postId;
    }
}

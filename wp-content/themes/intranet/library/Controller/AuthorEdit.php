<?php

namespace Intranet\Controller;

class AuthorEdit extends \Municipio\Controller\BaseController
{
    public function init()
    {
        global $wp_query;
        global $authordata;

        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if ($user) {
            $authordata = $user;
        }

        $this->data['currentUser'] = $currentUser;
        $this->data['user'] = $user;
    }
}

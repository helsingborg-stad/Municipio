<?php

namespace Municipio\Helper;

class Likes
{

    /**
     * Display comment like button
     * @param int $id Comment ID
     * @return string Markup to display button
     */
    public static function likeButton($id)
    {
        if (! is_user_logged_in()) {
            return;
        }

        $likes = get_comment_meta($id, '_likes', true);

        if (empty($likes) || is_array($likes) == false) {
            $count = 0;
        } else {
            $count = count($likes);
        }

        $classes = array('like-button');

        if (is_array($likes) == true && in_array(get_current_user_id(), $likes)) {
            $classes[] = 'active';
        }

        $output['classList'] = implode(' ', $classes);
        $output['count'] = $count;

        return $output;
    }


}

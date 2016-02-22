<?php

namespace Municipio\Controller;

class Single extends \Municipio\Controller\BaseController
{
    public function init()
    {
        global $post;
        $this->data['comments'] = get_comments(array(
            'post_id' => $post->ID
        ));
    }
}

<?php

namespace Municipio\Api;

class Navigation
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoint'));
    }

    public function registerEndpoint()
    {
        register_rest_route('municipio/v1', '/navigation/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'getSubmenu'),
        ));
    }

    public function getSubmenu($data)
    {
        //Switch blog if differ blog id
        if(isset($data['blog-id']) && is_numeric($data['blog-id'])) {
            if($data['blog-id'] != get_current_blog_id()) {
                switch_to_blog($data['blog-id']);
                $blogIdswitch = true;
            } else {
                $blogIdswitch = false;
            }
        }

        $submenu = new \Municipio\Helper\NavigationTree(
            array(
                'include_top_level' => false,
                'depth' => 2,
                'wrapper' => '%3$s'
            ),
            $data['id']
        );

        //Restore blog
        if($blogIdswitch) {
            restore_current_blog(); 
        }

        return '<ul class="sub-menu">' . $submenu->render(false) . '</ul>';
    }
}

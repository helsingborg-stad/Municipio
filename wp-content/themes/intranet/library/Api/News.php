<?php

namespace Intranet\Api;

class News
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'routes'));
    }

    public function routes()
    {
        register_rest_route('intranet/1.0', '/news/(?P<count>(.*)+)/(?P<offset>(.*)+)/(?P<sites>(.*)+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'getNews'),
            'args' => array(
                'count' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ),
                'offset' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ),
                'sites' => array(
                    'required' => true
                ),
            )
        ));
    }

    public function getNews($data)
    {
        $sites = $data['sites'];

        if ($sites !== 'all' || !is_null($sites)) {
            $sites = explode(',', $sites);
        }

        $news = \Intranet\CustomPostType\News::getNews($data['count'], $sites, $data['offset']);
        return $news;
    }
}

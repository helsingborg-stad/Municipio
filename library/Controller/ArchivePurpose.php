<?php

namespace Municipio\Controller;

class ArchivePurpose extends \Municipio\Controller\Archive
{
    public function init()
    {
        $postType = get_post_type();
        $purpose = \Municipio\Helper\Purpose::getPurpose($postType, true);

        echo '<pre>' . print_r($postType, true) . '</pre>';
        echo '<pre>' . print_r($purpose, true) . '</pre>';

        if (!empty($purpose) && !empty($postType)) {
            if ('place' === $purpose[0]->key || in_array('place', $purpose[0]->secondaryPurpose)) {
                add_filter(
                    'Municipio/Controller/Archive/getArchivePosts',
                    [$this, 'addLocationToArchivePosts'],
                    2,
                    1
                );
            }
        }

        parent::init();
    }

    public function addLocationToArchivePosts($posts)
    {
        echo '<pre>' . print_r($posts, true) . '</pre>';
        foreach ($posts as $_post) {
            echo '<pre>' . print_r($_post, true) . '</pre>';
            $location = get_field('location', $_post->id);
            echo '<pre>' . print_r($location, true) . '</pre>';
        }
        return $posts;
    }
}

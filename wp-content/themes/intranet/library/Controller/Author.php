<?php

namespace Intranet\Controller;

class Author extends \Intranet\Controller\BaseController
{
    private $user, $cover_url, $cover_classes;

    public function init()
    {
        global $wp_query;
        global $authordata;

        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if ($user) {
            $authordata = $user;
            $this->user = $user;
        }

        $this->getCoverUrl();
        //$this->setCoverClasses();

        $this->data['userResponsibilities'] = is_array(get_the_author_meta('user_responsibilities', $user->ID)) ? get_the_author_meta('user_responsibilities', $user->ID) : array();
        $this->data['userSkills'] = is_array(get_the_author_meta('user_skills', $user->ID)) ? get_the_author_meta('user_skills', $user->ID) : array();

        $this->data['cover_url'] = $this->cover_url;
        //$this->data['cover_classes'] = $this->cover_classes;
    }




    public function getCoverUrl()
    {
        $user = $this->user;

        if(isset($user)) {
            $profile_cover = get_the_author_meta('user_profile_cover', $user->ID);
        }

        if (isset($profile_cover) && !empty($profile_cover)) {
            $this->cover_url = $profile_cover;
        }

        else {
            $this->getDefaultCoverUrl();
        }
    }

    public function getDefaultCoverUrl()
    {
        $images = get_field('default_author_cover', 'options');

        if(isset($images) && $images) {
            $image = $images[array_rand($images, 1)]['cover'];
            $this->cover_url = $image['url'];
        }

        else {
            $this->cover_url = 'http://www.helsingborg.se/wp-content/uploads/2016/05/varen_2016_2_1800x350.jpg';
        }
    }

    public function setCoverClasses()
    {
        $classes = $this->cover_classes;

        if (isset($classes) && is_array($classes)) {
            $this->cover_classes = implode(' ', $classes);
        }
    }
}

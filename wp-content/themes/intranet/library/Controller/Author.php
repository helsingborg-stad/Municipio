<?php

namespace Intranet\Controller;

class Author extends \Intranet\Controller\BaseController
{
    private $user, $cover_url, $profile_img;

    public function init()
    {
        global $wp_query;
        global $authordata;

        $this->redirectProfile();

        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if ($user) {
            $authordata = $user;
            $this->user = $user;
        }

        $this->data['userResponsibilities'] = is_array(get_the_author_meta('user_responsibilities', $user->ID)) ? get_the_author_meta('user_responsibilities', $user->ID) : array();
        $this->data['userSkills'] = is_array(get_the_author_meta('user_skills', $user->ID)) ? get_the_author_meta('user_skills', $user->ID) : array();

        $this->data['cover_url'] =  $this->getCoverUrl();
        $this->data['profile_img'] = $this->getProfileImg();
    }

    /**
     * Redirect to main blog author page
     * @return void
     */
    public function redirectProfile()
    {
        //Check if main site
        if(get_current_blog_id() == BLOG_ID_CURRENT_SITE) {
            return;
        }

        //Redirect to main site
        wp_redirect(municipio_intranet_get_user_profile_url(get_queried_object()->ID));
        exit;
    }

    /**
     * Get cover URL
     * @return mixed string/array
     */
    public function getCoverUrl()
    {
        $user = $this->user;

        if(isset($user)) {
            $profile_cover = get_the_author_meta('user_profile_cover', $user->ID);
        }

        if (isset($profile_cover) && !empty($profile_cover)) {
            return $profile_cover[0];
        }

        else {
            $images = get_field('default_author_cover', 'options');

            //First fallback (ACF)
            if(isset($images) && $images) {
                $image = $images[array_rand($images, 1)]['cover'];
                $image = wp_get_attachment_image_src($image['ID'], array('1366','768'));

                return $image[0];
            }

            //Last fallback (STATIC)
            return 'http://www.helsingborg.se/wp-content/uploads/2016/05/varen_2016_2_1800x350.jpg';
        }
    }

    /**
     * Get profile image URL
     * @return mixed string/array
     */
    public function getProfileImg()
    {
        if(isset($this->user) && $this->user) {
            return get_user_meta($this->user->data->ID, 'user_profile_picture', true);
        }
    }

}

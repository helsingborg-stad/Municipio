<?php

namespace Intranet\Controller;

class AuthorEdit extends \Intranet\Controller\BaseController
{
    public function init()
    {
        global $wp_query;
        global $authordata;

        // Save form if posted
        do_action('MunicipioIntranet/save_profile_settings');

        // Get other data
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if ($user) {
            $authordata = $user;
        }

        $this->data['user'] = $user;
        $this->data['userResponsibilities'] = is_array(get_the_author_meta('user_responsibilities', $user->ID)) ? get_the_author_meta('user_responsibilities', $user->ID) : array();
        $this->data['userSkills'] = is_array(get_the_author_meta('user_skills', $user->ID)) ? get_the_author_meta('user_skills', $user->ID) : array();
        $this->data['administrationUnits'] = \Intranet\User\AdministrationUnits::getAdministrationUnits();
        $this->data['targetGroups'] = \Intranet\User\TargetGroups::getAvailableGroups(false, get_current_user_id());
        $this->data['profile_img'] =   $this->getProfileImageUrl($user->ID);
        $this->data['cover_img'] = $this->getCoverUrl($user->ID);
    }

    /**
     * Get profile cover URL
     * @param  int User ID
     * @return string
     */
    private function getCoverUrl($userId)
    {
        $image = get_the_author_meta('user_profile_cover', $userId);

        if(! isset($image)) {
            return;
        }

        if(is_array($image)) {
            return $image[0];
        }

        return $image;
    }

    /**
     * Get profile img URL
     * @param  int User ID
     * @return string
     */
    private function getProfileImageUrl($userId)
    {
        $image = get_the_author_meta('user_profile_img', $userId);

        if(isset($image) && is_array($image)) {
            return $image[1];
        }
        //CHECK FOR OLD META FIELD
        else {
             $image = get_the_author_meta('user_profile_picture', $userId);
        }

        if(isset($image)) {
            return $image;
        }

    }
}

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
        $this->data['profile_img'] = get_the_author_meta('user_profile_picture', $user->ID);
    }
}

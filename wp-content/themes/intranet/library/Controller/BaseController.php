<?php

namespace Intranet\Controller;

class BaseController extends \Municipio\Controller\BaseController
{
    public function __construct()
    {
        $this->missingUserData();
        $this->updateProfileReminder();

        parent::__construct();
    }

    /**
     * Show missung userdata notice if user is missing any data
     * @return void
     */
    public function missingUserData()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $this->data['missing'] = array_merge(
            \Intranet\User\Data::missingRequiredUserData(),
            \Intranet\User\Data::missingRequiredFields()
        );

        $this->data['show_userdata_guide'] = false;
        if (is_array($this->data['missing']) && count($this->data['missing']) > 0 && !\Intranet\User\Data::$muteFillerForm) {
            $this->data['show_userdata_guide'] = true;
        }

        $this->data['missing'] = array_merge($this->data['missing'], \Intranet\User\Data::missingSuggestedFields());

        $this->data['show_userdata_notice'] = false;
        if (!empty($this->data['missing']) && !$this->data['show_userdata_guide']) {
            $this->data['show_userdata_notice'] = true;
        }
    }

    /**
     * Show "update profile reminder" if it's more than 181 days since last update
     * @return void
     */
    public function updateProfileReminder()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $this->data['show_update_profile_reminder'] = false;

        $lastUpdated = get_user_meta(get_current_user_id(), '_profile_updated', true);
        if (!$lastUpdated) {
            return;
        }

        $lastUpdated = date_create($lastUpdated);
        $now = date_create(date('Y-m-d H:i:s'));
        $diff = date_diff($lastUpdated, $now);

        if ($diff->days < 182) {
            return;
        }

        $this->data['show_update_profile_reminder'] = true;

    }
}

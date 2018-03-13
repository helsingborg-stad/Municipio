<?php

namespace Municipio\Admin\UI;

class FrontEnd
{
    public function __construct()
    {
        add_action('admin_bar_init', array($this, 'removeAdminBarStyle'));
        add_action('wp_head', array($this, 'customAdminBarStyle'));
    }


    public function removeAdminBarStyle()
    {
        remove_action('wp_head', '_admin_bar_bump_cb');
    }

    /**
     * Add margin top to body instead of html
     * when admin bar is showing
     */
    public function customAdminBarStyle()
    {
        if (is_user_logged_in() && is_admin_bar_showing()) {
            ?>
		<style type="text/css">
		body { margin-top: 32px !important; }
		* html body { margin-top: 32px !important; }
		@media screen and ( max-width: 782px ) {
			body { margin-top: 46px !important; }
			* html body { margin-top: 46px !important; }
		}
		</style>
		<?php
        }
    }
}

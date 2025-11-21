<?php

namespace Modularity\Helper;

class Acf
{
    public function __construct()
    {
        add_action('init', [$this, 'includeAcf'], 11);
        add_filter('acf/settings/l10n', static function () {
            return true;
        });
    }

    /**
     * Includes Advanced Custom Fields if missing, notifies user to activate ACF PRO to get full expirience
     * @return void
     */
    public function includeAcf()
    {
        if (!(class_exists('acf_pro') || class_exists('ACF'))) {
            require_once MODULARITY_PATH . 'plugins/acf/acf.php';

            add_action('admin_notices', static function () {
                echo
                    '<div class="notice error"><p>'
                    . __(
                            'To get the full expirience of the <strong>Modularity</strong> plugin, please activate the <a href="http://www.advancedcustomfields.com/pro/" target="_blank">Advanced Custom Fields Pro</a> plugin.',
                            'municipio',
                        )
                        . '</p></div>'
                ;
            });
        }
    }
}

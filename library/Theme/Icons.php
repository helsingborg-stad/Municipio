<?php

namespace Municipio\Theme;

class Icons
{
    /**
     * Get list of pricons from remote Json
     *
     * @return array               List of pricons and values
     */
    public static function getPricons()
    {
        if (!defined('MUNICIPIO_STYLEGUIDE_URI')) {
            return false;
        }

        $transientKey = apply_filters('Municipio/Theme/Icons/Pricons/TransientKey', 'hbg_pricons');

        if (get_site_transient($transientKey)) {
            return get_site_transient($transientKey);
        }

        $url = apply_filters('Municipio/Theme/Icons/Pricons/Url', 'https:' . MUNICIPIO_STYLEGUIDE_URI . 'pricons.json');
        $json = \Municipio\Helper\Data::getRemoteJson($url);

        if ($json && is_array($json) && !empty($json)) {
            $pricons = array();

            //Convert values to array
            foreach ($json as $icon) {
                $pricons[$icon->name] = get_object_vars($icon);
            }

             set_site_transient($transientKey, $pricons, 10);
             return get_site_transient($transientKey);
        }

        return false;
    }
}

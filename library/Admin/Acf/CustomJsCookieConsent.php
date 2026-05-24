<?php
namespace Municipio\Admin\Acf;

use Municipio\Helper\CookieConsent;

class CustomJsCookieConsent
{
    public function __construct()
    {
        // Hide additional consent fields if the Pressidium plugin is not enabled
        if (is_admin() && !CookieConsent::pressidiumIsEnabled()) {
            add_filter('acf/load_field/name=custom_js_tags_cookie_consents', function ($field) {
                $field['wrapper']['class'] = 'hidden';

                return $field;
            });
        }
    }
}
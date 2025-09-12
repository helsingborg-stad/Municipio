<?php

namespace Municipio\Theme;

class Forms
{
    public function __construct()
    {
        add_filter('the_password_form', array($this, 'thePasswordForm'), 5, 2);
        add_filter('the_password_form', array($this, 'thePasswordFormError'), 10, 2);
    }

    public function thePasswordForm(string $output, $post): string
    {
        remove_filter( 'the_content', 'wpautop' );
        return render_blade_view(
            'partials.forms.password',
            [
                'formAction'         => esc_url(site_url('wp-login.php?action=postpass', 'login_post')),
                'messageBefore'      => __('This content is password protected. To view it please enter your password below:'),
                'passwordFieldLabel' => __('Password'),
                'submitBtnValue'     => esc_attr_x('Enter', 'post password form'),
            ]
        );
    }

    public function thePasswordFormError(string $output, $post): string
    {
        if (!isset($_COOKIE['wp-postpass_' . COOKIEHASH])) {
            return $output;
        }

        if (!is_singular()) {
            return $output;
        }

        if (!wp_get_raw_referer() == get_permalink()) {
            return $output;
        }

        return render_blade_view(
            'partials.forms.password-error',
            [
                'message' => __("The password entered is not correct.", 'municipio')
            ]
        ) . $output;
    }
}
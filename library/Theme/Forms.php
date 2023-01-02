<?php

namespace Municipio\Theme;

class Forms
{
    public function __construct()
    {
        add_filter('the_password_form', array($this, 'thePasswordForm'), 0, 2);
    }

    public function thePasswordForm(string $output, $post): string
    {
        return render_blade_view(
            'partials.forms.password',
            [
                'formAction'      => esc_url(site_url('wp-login.php?action=postpass', 'login_post')),
                'messageBefore'   => __('This content is password protected. To view it please enter your password below:'),
                'passwordFieldLabel' => __('Password'),
                'submitBtnValue'  => esc_attr_x('Enter', 'post password form'),
            ]
        );
    }
}

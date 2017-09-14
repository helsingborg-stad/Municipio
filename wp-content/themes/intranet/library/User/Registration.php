<?php

namespace Intranet\User;

class Registration
{
    protected $defaultRole = 'subscriber';

    public function __construct()
    {
        // Do not allow "special accounts"
        add_filter('pre_user_login', array($this, 'disallowedUsers'));

        // Ban new user account registration if email is used as username
        add_filter('pre_user_login', array($this, 'disallowAccountRegistration'));

        // Set default display name
        add_action('network_user_new_form', array($this, 'addNameFieldsToUserRegistration'));
        add_action('wpmu_new_user', array($this, 'saveUserRegistrationName'), 10, 1);

        // Auto subscribe to intranets matching ad_displayname end tag
        add_action('wpmu_new_user', array($this, 'autosubscribe'));
        add_action('wp_login', array($this, 'autosubscribe'));
    }

    /**
     * Disallow username combinations
     * Example: S-accounts
     * @param  string $userLogin    User's username
     * @return void
     */
    public function disallowedUsers($userLogin)
    {
        if (wp_doing_cron()) {
            return $userLogin;
        }

        // If pattern matches it will be considered disallowed
        // pattern => redirect
        $disallowed = apply_filters('MunicipioIntranet/register/disallowed', array(
            '/^s([a-z]{4})([0-9]{4})/i' => network_home_url('?login=saccount'),
            '/^([a-z]{3})([0-9]{4})/i' => network_home_url('?login=faccount'),
            '/^([0-9]{6})([a-z]{2})/i' => network_home_url('?login=schoolaccount')
        ));

        // Loop disallowed patterns, fail-redirect if pattern matches
        foreach ($disallowed as $pattern => $redirectUrl) {
            if (!preg_match($pattern, $userLogin)) {
                continue;
            }

            setcookie('sso_manual_logout', true, time()+3600, '/', COOKIE_DOMAIN);
            wp_redirect($redirectUrl);
            exit;
        }

        // Not disallowed, return userLogin
        return $userLogin;
    }

    public function disallowAccountRegistration($userLogin)
    {
        if (is_email($userLogin) && !email_exists($email)) {
            wp_redirect(network_home_url('?login=noemail'));
            exit;
        }

        return $userLogin;
    }

    /**
     * Adds the firstname and lastname fields to the network user registration
     */
    public function addNameFieldsToUserRegistration()
    {
        echo '
            <table class="form-table">
                <tr class="form-field form-required">
                    <th scope="row"><label for="first_name">' . __('First name') . '</label></th>
                    <td><input type="text" class="regular-text" name="user[first_name]" id="first_name" autocapitalize="true" autocorrect="off" maxlength="60" required></td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row"><label for="last_name">' . __('Last name') . '</label></th>
                    <td><input type="text" class="regular-text" name="user[last_name]" id="last_name" autocapitalize="true" autocorrect="off" maxlength="60" required></td>
                </tr>
            </table>
        ';
    }

    /**
     * Autosubscribe to the users main intranet on registration
     * @param  integer $userId User id
     * @return void
     */
    public function autosubscribe($userId)
    {
        if (!is_numeric($userId)) {
            $user = get_user_by('login', $userId);

            if (!$user) {
                return false;
            }

            $userId = $user->ID;
        }

        $adTag = get_user_meta($userId, 'ad_displayname', true);
        $adTag = explode('-', $adTag);
        $adTag = strtolower(trim(end($adTag)));

        $sites = \Intranet\Helper\Multisite::getSitesList();

        foreach ($sites as $key => $site) {
            if (!$site->autosubscribe_tags) {
                continue;
            }

            $siteTags = explode(',', $site->autosubscribe_tags);
            $siteTags = array_map(function ($item) {
                return strtolower(trim($item));
            }, $siteTags);

            if (!in_array($adTag, $siteTags)) {
                continue;
            }

            \Intranet\User\Subscription::subscribe($userId, $site->blog_id);
        }
    }

    /**
     * Save the user's firstname and lastname on network registration
     * @param  integer $userId The user's id
     * @return void
     */
    public function saveUserRegistrationName($userId)
    {
        $firstName = isset($_POST['user']['first_name']) && !empty($_POST['user']['first_name']) ? sanitize_text_field($_POST['user']['first_name']) : '';
        $lastName = isset($_POST['user']['last_name']) && !empty($_POST['user']['last_name']) ? sanitize_text_field($_POST['user']['last_name']) : '';

        update_user_meta($userId, 'first_name', $firstName);
        update_user_meta($userId, 'last_name', $lastName);

        wp_update_user(array(
            'ID' => $userId,
            'display_name' => $firstName . ' ' . $lastName
        ));
    }
}

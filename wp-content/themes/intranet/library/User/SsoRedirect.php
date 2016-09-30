<?php

namespace Intranet\User;

class SsoRedirect
{

    private $ssoEnabledIpAdress;
    private $prohibitedUrls;
    public $settings;

    public function __construct()
    {
        //Set vars
        $this->ssoEnabledIpAdress = array('10.','192.');
        $this->prohibitedUrls = array('plugins');

        //Run code (if not prohibited url)
        if(!$this->disabledUrl()) {
            add_action('init', array($this,'init'), 9999);
        }
    }

    public function init()
    {
        if(!$this->isAuthenticated() && $this->isInNetwork() && $this->isExplorer()) {
            $this->doAuthentication();
        } elseif(!$this->isInNetwork() || !$this->isExplorer()) {
            add_filter('option_active_plugins', array($this, 'disableSsoPlugin'));
        } elseif($this->isAuthenticated()) {
            add_filter('body_class', array($this, 'addBodyClass'));
        }
    }

    public function isAuthenticated()
    {
        return is_user_logged_in();
    }

    public function isInNetwork()
    {
        foreach($this->ssoEnabledIpAdress as $ip) {
            if (0 === strpos($_SERVER['REMOTE_ADDR'], $ip)) {
                return true;
            }
        }
        return false;
    }

    public function isExplorer()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7')) {
            return true;
        }
        return false;
    }

    public function doAuthentication()
    {
        if(class_exists('SAML_Client')) {
            $client = new SAML_Client();
            $client->authenticate();
        } elseif( (defined('WP_DEBUG') && WP_DEBUG === true) && function_exists('write_log')) {
            write_log('Error: SAML client plugin is not active.');
        }
    }

    public function disabledUrl() {
        foreach($this->prohibitedUrls as $url) {
            if (false !== strpos($_SERVER['REQUEST_URI'], $url)) {
                return true;
            }
        }
        return false;
    }

    public function disableSsoPlugin($plugins){
        $key = array_search( 'saml-20-single-sign-on/samlauth.php' , $plugins );
        if ( false !== $key ) unset( $plugins[$key] );
        return $plugins;
    }

    public function addBodyClass($classes) {
        return array_merge( $classes, array( 'sso-enabled' ));
    }
}

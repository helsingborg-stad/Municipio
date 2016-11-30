<?php
class SAML_Client
{
  private $saml;
  private $opt;

  function __construct()
  {
    $this->settings = new SAML_Settings();

    require_once(constant('SAMLAUTH_ROOT') . '/saml/lib/_autoload.php');
    if( $this->settings->get_enabled() )
    {
      $this->saml = new SimpleSAML_Auth_Simple((string)get_current_blog_id());

      add_action('wp_authenticate',array($this,'authenticate'));
      add_action('wp_logout',array($this,'logout'));
      add_action('login_form', array($this, 'modify_login_form'));
    }
  }

  /**
   *  Authenticates the user using SAML
   *
   *  @return void
   */
  public function authenticate()
  {
    if( isset($_GET['loggedout']) && $_GET['loggedout'] == 'true' )
    {
      header('Location: ' . get_option('siteurl'));
      exit();
    }
    elseif ( $this->settings->get_allow_sso_bypass() == true  && (( isset($_GET['use_sso']) && $_GET['use_sso'] == 'false' ) || ( isset($_POST['use_sso']) && $_POST['use_sso'] == 'false' )) )
    {
      // User wants native WP login, do nothing
    }
    else
    {

      $redirect_url = (array_key_exists('redirect_to', $_GET)) ? wp_login_url( $_GET['redirect_to']) : get_admin_url();
      $this->saml->requireAuth( array('ReturnTo' => $redirect_url ) );
      $attrs = $this->saml->getAttributes();
      if(array_key_exists($this->settings->get_attribute('username'), $attrs) )
      {
        $username = $attrs[$this->settings->get_attribute('username')][0];
        if(get_user_by('login',$username))
        {
          $this->simulate_signon($username);
        }
        else
        {
          $this->new_user($attrs);
        }
      }
      else
      {
        die('A username was not provided.');
      }
    }
  }

  /**
   * Sends the user to the SAML Logout URL (using SLO if available) and then redirects to the site homepage
   *
   * @return void
   */
  public function logout()
  {
    $this->saml->logout( get_option('siteurl') );
  }

  /**
   * Runs about halfway through the login form. If we're bypassing SSO, we need to add a field to the form
   *
   * @return void
   */
  public function modify_login_form() {
    if( array_key_exists('use_sso', $_GET) && $_GET['use_sso'] == 'false' && $this->settings->get_allow_sso_bypass() == true )
    {
      echo '<input type="hidden" name="use_sso" value="false">'."\n";
    }
  }

  /**
   * Creates a new user in the WordPress database using attributes from the IdP
   *
   * @param array $attrs The array of attributes created by SimpleSAMLPHP
   * @return void
   */
  private function new_user($attrs)
  {
    if( array_key_exists($this->settings->get_attribute('username'),$attrs) )
    {
      $login = (array_key_exists($this->settings->get_attribute('username'),$attrs)) ? $attrs[$this->settings->get_attribute('username')][0] : 'NULL';
      $email = (array_key_exists($this->settings->get_attribute('email'),$attrs)) ? $attrs[$this->settings->get_attribute('email')][0] : '';
      $first_name = (array_key_exists($this->settings->get_attribute('firstname'),$attrs)) ? $attrs[$this->settings->get_attribute('firstname')][0] : '';
      $last_name = (array_key_exists($this->settings->get_attribute('lastname'),$attrs)) ? $attrs[$this->settings->get_attribute('lastname')][0] : '';
      $display_name = $first_name . ' ' . $last_name;
    }
    else
    {
      die('A username was not provided.');
    }

    $role = $this->update_role();

    if( $role !== false )
    {
      $user_opts = array(
        'user_login' => $login ,
        'user_pass'  => $this->random_password(),
        'user_email' => $email ,
        'first_name' => $first_name ,
        'last_name'  => $last_name ,
        'display_name' => $display_name ,
        'role'       => $role
        );
      wp_insert_user($user_opts);
      $this->simulate_signon($login);
    }
    else
    {
      die('The website administrator has not given you permission to log in.');
    }
  }

  /**
   * Authenticates the user with WordPress using wp_signon()
   *
   * @param string $username The user to log in as.
   * @return void
   */
  private function simulate_signon($username)
  {
    $this->update_role();
    $user = get_user_by('login', $username);

    if (class_exists('adLDAP')) {
        $connArgs = array(
            "base_dn" => get_site_option('AD_Integration_base_dn'),
            "domain_controllers" => explode(';', get_site_option('AD_Integration_domain_controllers')),
            "ad_port" => get_site_option('AD_Integration_port'),                  // AD port
            "use_tls" => get_site_option('AD_Integration_use_tls'),                 // secure?
            "network_timeout" => (int)get_site_option('AD_Integration_network_timeout'),  // network timeout*/
            "ad_username" => get_site_option('AD_Integration_bulkimport_user'),     // Bulk Import User
            "ad_password" => $this->_decrypt(get_site_option('AD_Integration_bulkimport_pwd'))
        );

        $ad = new adLDAP($connArgs);

         $attributes =  array (
            'cn', // Common Name
            'givenname', // First name
            'sn', // Last name
            'displayname',  // Display name
            'description', // Description
            'mail', // E-mail
            'samaccountname', // User logon name
            'userprincipalname', // userPrincipalName
            'useraccountcontrol' // userAccountControl
        );
        $additionalFields = get_site_option('AD_Integration_additional_user_attributes');

        if (trim($additionalFields) != '') {
          $lines = explode("\n", str_replace("\r",'',$additionalFields));
          foreach ($lines AS $line) {
            $parts = explode(":",$line);
            if ($parts[0] != '') {
              if (!in_array($parts[0], $attributes)) {
                $attributes[] = $parts[0];
              }
            }
          }
        }

        $userinfo = $ad->user_info($username, $attributes);
        $userinfo = $userinfo[0];

        foreach ($attributes as $attribute) {
          if (!isset($userinfo[$attribute][0])) {
            continue;
          }

          update_user_meta($user->ID, 'ad_' . $attribute, $userinfo[$attribute][0]);
        }
      }

    wp_set_auth_cookie($user->ID);
    do_action('wp_login', $username, $user);

    if( array_key_exists('redirect_to', $_GET) )
    {
      wp_redirect( $_GET['redirect_to'] );
    }
    else
    {
      wp_redirect(network_home_url('/'));
    }
    exit();
  }

  protected function _decrypt($encrypted_text) {
    $encrypted_text = base64_decode($encrypted_text);
    if (function_exists('mcrypt_decrypt')) {
        $iv = md5('Active-Directory-Integration');
        $key = substr(AUTH_SALT,0, mcrypt_get_key_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB));
        $text = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted_text, MCRYPT_MODE_ECB, $iv), "\0");
    } else {
      $text = $encrypted_text;
    }
    return $text;
  }

  /**
   * Updates a user's role if their current one doesn't match the attributes provided by the IdP
   *
   * @return string
   */
  private function update_role()
  {
    $attrs = $this->saml->getAttributes();
    if(array_key_exists($this->settings->get_attribute('groups'), $attrs) )
    {
      foreach(wp_roles()->roles as $role_name => $role_meta){
          if( in_array($this->settings->get_group($role_name),$attrs[$this->settings->get_attribute('groups')]) )
          {
            $role = $role_name;
          }
      }
      if(isset($role)){}
      elseif( $this->settings->get_allow_unlisted_users() )
      {
        $role = 'subscriber';
      }
      else
      {
        $role = false;
      }
    }
    else
    {
      $role = false;
    }

    $user = get_user_by('login',$attrs[$this->settings->get_attribute('username')][0]);
    if($user)
    {
      $user->set_role($role);
    }

    return $role;
  }

  /**
   * Return a big random string. Depends on OpenSSL.
   */
  private function random_password() {
    return openssl_random_pseudo_bytes(64);
  }

  public function show_password_fields($show_password_fields) {
    return false;
  }

  public function disable_function() {
    die('Disabled');
  }
} // End of Class SamlAuth

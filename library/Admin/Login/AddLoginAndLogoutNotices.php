<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User;

class AddLoginAndLogoutNotices implements Hookable
{
    public function __construct(private WpService $wpService, private AcfService $acfService)
    {
    }

  /**
   * Add hooks
   */
    public function addHooks(): void
    {

      //Logon and logout notices
        $this->wpService->addAction('init', array($this, 'addNoticeWhenUserLogsIn'));
        $this->wpService->addAction('init', array($this, 'addNoticeWhenUserLogsOut'));

      // Offer persistant
        $this->wpService->addAction('init', array($this, 'offerPersistantGroupUrl'));
        $this->wpService->addAction('init', array($this, 'offerPersistantHomeUrl'));

      // Set persistant url
        $this->wpService->addAction('init', array($this, 'setPersistantGroupUrl'));
        $this->wpService->addAction('init', array($this, 'setPersistantHomeUrl'));
    }

    /**
     * Offer persistant group url
     * 
     * @return void
     */
    public function offerPersistantGroupUrl()
    {
        if ((bool)($_GET['offerPersistantGroupUrl'] ?? false)) {
          \Municipio\Helper\Notice::add(
            __('Login successful', 'municipio'),
            'info',
            'login',
            [
            'url'  => './?setPersistantGroupUrl=true',
            'text' => __('Save this page as default home', 'municipio')
            ],
            'session'
          );
        }
    }

    /**
     * Offer persistant home url
     * 
     * @return void
     */
    public function offerPersistantHomeUrl()
    {
        if ((bool)($_GET['offerPersistantHomeUrl'] ?? false)) {
          \Municipio\Helper\Notice::add(
            __('Login successful', 'municipio'),
            'info',
            'login',
            [
            'url'  => './?setPersistantHomeUrl=true',
            'text' => __('Save this page as default home', 'municipio')
            ],
            'session'
          );
        }
    }

    /**
     * Set persistant group url
     * 
     * @return void
     */
    public function setPersistantGroupUrl()
    {
        if ((bool)($_GET['setPersistantGroupUrl'] ?? false) && $this->wpService->isUserLoggedIn()) {
          $result = $this->wpService->updateUserMeta(
            $this->wpService->getCurrentUserId(),
            'user_prefers_group_url',
            true
          );

          if($result) {
            \Municipio\Helper\Notice::add(
              __('Option saved', 'municipio'), 'info', 'home'
            );
          } else {
            \Municipio\Helper\Notice::add(
              __('Option could not be saved at the moment', 'municipio'), 'warning', 'home'
            );
          }
        }
    }

    /**
     * Set persistant home url
     * 
     * @return void
     */
    public function setPersistantHomeUrl()
    {
        if ((bool)($_GET['setPersistantHomeUrl'] ?? false) && $this->wpService->isUserLoggedIn()) {
          $result = $this->wpService->deleteUserMeta(
            $this->wpService->getCurrentUserId(),
            'user_prefers_group_url'
          );

          if($result === true) {
            \Municipio\Helper\Notice::add(__('Option saved', 'municipio'), 'info', 'home');
          } else {
            \Municipio\Helper\Notice::add(__('Option could not be saved at the moment', 'municipio'), 'warning', 'home');
          }
        }
    }

  /**
   * Add notice when user logs in
   */
    public function addNoticeWhenUserLogsIn()
    {
        if ((bool)($_GET['loggedin'] ?? false) && $this->wpService->isUserLoggedIn()) {
            $currentUserGroup    = User::getCurrentUserGroup();
            $currentUserGroupUrl = User::getCurrentUserGroupUrl($currentUserGroup);
            $userPrefersGroupUrl = User::getUserPrefersGroupUrl();

            // If user has no group (or no url is set), show default message
            if(!$currentUserGroupUrl) {
                \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login');
                return;
            }

            // If user has a group and prefers group url, show message with group url
            if ($currentUserGroupUrl && !$userPrefersGroupUrl) {
                $this->messageWhenUserPrefersUserGroupUrl($currentUserGroup, $currentUserGroupUrl);
            } else {
                $this->messageWhenUserDoesNotPreferUserGroupUrl($currentUserGroup, $currentUserGroupUrl);
            }
        }
    }

    /**
     * Show message with user group
     *
     * @param WP_Term $currentUserGroup
     * @param string $currentUserGroupUrl
     */
    private function messageWhenUserPrefersUserGroupUrl($currentUserGroup, $currentUserGroupUrl)
    {
      \Municipio\Helper\Notice::add(
        __('Login successful', 'municipio'),
        'info',
        'login',
        [
        'url'  => $this->addQueryParamsToUrl(
          $currentUserGroupUrl,
          ['offerPersistantGroupUrl' => 'true']
        ),
        'text' => __('Go to', 'municipio') . ' ' . $currentUserGroup->name ?? __('home', 'municipio')
        ],
        'session'
      );
    }

    /**
     * Show message without user group
     *
     * @param WP_Term $currentUserGroup
     * @param string $currentUserGroupUrl
     */
    private function messageWhenUserDoesNotPreferUserGroupUrl($currentUserGroup, $currentUserGroupUrl)
    {
      \Municipio\Helper\Notice::add(
        __('Login successful', 'municipio'),
        'info',
        'login',
        [
        'url'  => $this->addQueryParamsToUrl(
          $this->wpService->homeUrl(),
          ['offerPersistantHomeUrl' => 'true']
        ),
        'text' => __('Go to main site', 'municipio'),
        ],
        'session'
      );
    }

  /**
   * Add notice when user logs out
   */
    public function addNoticeWhenUserLogsOut()
    {
        if ((bool)($_GET['loggedout'] ?? false) && !$this->wpService->isUserLoggedIn()) {
            \Municipio\Helper\Notice::add(__('Logout successful', 'municipio'), 'info', 'logout');
        }
    }

    private function addQueryParamsToUrl(string $url, array $params): string
    {
      return add_query_arg($params, $url);
    }
    
}

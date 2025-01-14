<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\Helper\User\Config\UserConfig;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User\User;

class AddLoginAndLogoutNotices implements Hookable
{
    public function __construct(private WpService $wpService, private AcfService $acfService, private User $userHelper, private UserConfig $userConfig)
    {
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
        //Set current user
        add_action('init', [$this->userHelper, 'setUser'], 5, 0);

        // Logon and logout notices
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
     */
    public function offerPersistantGroupUrl(): void
    {
        if ((bool)($_GET['offerPersistantGroupUrl'] ?? false)) {
            \Municipio\Helper\Notice::add(
                '',
                'info',
                'save',
                [
                    'url'  => './?setPersistantGroupUrl=true&uid=' . $this->wpService->getCurrentUserId(),
                    'text' => $this->wpService->__('Save this page as default home', 'municipio'),
                ],
                'session'
            );
        }
    }

    /**
     * Offer persistant home url
     */
    public function offerPersistantHomeUrl(): void
    {
        if ((bool)($_GET['offerPersistantHomeUrl'] ?? false)) {
            \Municipio\Helper\Notice::add(
                '',
                'info',
                'save',
                [
                    'url'  => './?setPersistantHomeUrl=true&uid=' . $this->wpService->getCurrentUserId(),
                    'text' => $this->wpService->__('Save this page as default home', 'municipio'),
                ],
                'session'
            );
        }
    }

    /**
     * Set persistant group url
     */
    public function setPersistantGroupUrl(): void
    {
        if ((bool)($_GET['setPersistantGroupUrl'] ?? false) && $this->wpService->isUserLoggedIn()) {
            $result = $this->wpService->updateUserMeta(
                $this->wpService->getCurrentUserId(),
                $this->userConfig->getUserPrefersGroupUrlMetaKey(),
                true
            );

            $message = $result
                ? $this->wpService->__('Option saved', 'municipio')
                : $this->wpService->__('Option already saved', 'municipio');

            $icon = $result ? 'check_circle' : 'preliminary';

            \Municipio\Helper\Notice::add($message, 'info', $icon);
        }
    }

    /**
     * Set persistant home url
     */
    public function setPersistantHomeUrl(): void
    {
        if ((bool)($_GET['setPersistantHomeUrl'] ?? false) && $this->wpService->isUserLoggedIn()) {
            $result = $this->wpService->deleteUserMeta(
                $this->wpService->getCurrentUserId(),
                $this->userConfig->getUserPrefersGroupUrlMetaKey(),
            );

            $message = $result
                ? $this->wpService->__('Option saved', 'municipio')
                : $this->wpService->__('Option could not be saved at the moment', 'municipio');

            $type = $result ? 'info' : 'warning';
            $icon = $result ? 'check_circle' : 'preliminary';

            \Municipio\Helper\Notice::add($message, $type, $icon);
        }
    }

    /**
     * Add notice when user logs in
     */
    public function addNoticeWhenUserLogsIn(): void
    {
        if ((bool)($_GET['loggedin'] ?? false) && $this->wpService->isUserLoggedIn()) {
            $currentUserGroup        = $this->userHelper->getUserGroup();
            $currentUserGroupUrl     = $this->userHelper->getUserGroupUrl();
            $userPrefersGroupUrl     = $this->userHelper->getUserPrefersGroupUrl();
            $userPrefersGroupUrlType = $this->userHelper->getUserGroupUrlType();

            // No url to prefer
            if (!$currentUserGroupUrl) {
                \Municipio\Helper\Notice::add($this->wpService->__('Login successful', 'municipio'), 'info', 'login');
                return;
            }

            // User prefers group url, and are given option to remove this setting
            if ($this->shouldOfferRemovingGroupUrlAsHome($currentUserGroupUrl, $userPrefersGroupUrl)) {
                $this->messageWhenUserPrefersUserGroupUrl($currentUserGroup, $currentUserGroupUrl);
                return;
            }

            // User does not prefer group url, and are given option to set this as home
            if ($this->shouldOfferSettingGroupUrlAsHome($currentUserGroupUrl, $userPrefersGroupUrl)) {
                $this->messageWhenUserDoesNotPreferUserGroupUrl(
                    $userPrefersGroupUrlType
                );
                return;
            }
        }
    }

    /**
     * Show message with user group
     */
    private function messageWhenUserPrefersUserGroupUrl(\WP_Term $currentUserGroup, string $currentUserGroupUrl): void
    {
        \Municipio\Helper\Notice::add(
            $this->wpService->__('Login successful', 'municipio'),
            'info',
            'login',
            [
                'url'  => $this->addQueryParamsToUrl(
                    $currentUserGroupUrl,
                    ['offerPersistantHomeUrl' => 'true']
                ),
                'text' => sprintf(
                    $this->wpService->__('Go to %s', 'municipio'),
                    $currentUserGroup->name ?? $this->wpService->__('home', 'municipio')
                ),
            ],
            'session'
        );
    }

    /**
     * Show message without user group
     */
    private function messageWhenUserDoesNotPreferUserGroupUrl(string $urlType): void
    {
        //Get network main site url if link type is blog_id
        if ($urlType == 'blog_id' && $this->wpService->isMultisite()) {
            $url = $this->wpService->getHomeUrl($this->wpService->getMainSiteId() ?? null);
        } else {
            $url = $this->wpService->getHomeUrl();
        }

        //Add notice
        \Municipio\Helper\Notice::add(
            $this->wpService->__('Login successful', 'municipio'),
            'info',
            'login',
            [
                'url'  => $this->addQueryParamsToUrl(
                    $url,
                    [
                      'offerPersistantGroupUrl' => 'true'
                    ]
                ),
                'text' => $this->wpService->__('Go to default home', 'municipio'),
            ],
            'session'
        );
    }

    /**
     * Add notice when user logs out
     *
     * @return void
     */
    public function addNoticeWhenUserLogsOut(): void
    {
        if ((bool)($_GET['loggedout'] ?? false) && !$this->wpService->isUserLoggedIn()) {
            \Municipio\Helper\Notice::add($this->wpService->__('Logout successful', 'municipio'), 'info', 'logout');
        }
    }

    /**
     * Add query params to url
     *
     * @param string $url
     * @param array $params
     *
     * @return string
     */
    private function addQueryParamsToUrl(string $url, array $params): string
    {
        return add_query_arg($params, $url);
    }

    /**
     * Should offer setting group url as home
     *
     * @return bool
     */
    private function shouldOfferSettingGroupUrlAsHome($currentUserGroupUrl, $userPrefersGroupUrl): bool
    {
        return ($currentUserGroupUrl && !$userPrefersGroupUrl);
    }

    /**
     * Should offer removing group url as home,
     * a negative of shouldOfferSettingGroupUrlAsHome
     *
     * @return bool
     */
    private function shouldOfferRemovingGroupUrlAsHome($currentUserGroupUrl, $userPrefersGroupUrl): bool
    {
        return !$this->shouldOfferSettingGroupUrlAsHome($currentUserGroupUrl, $userPrefersGroupUrl);
    }
}

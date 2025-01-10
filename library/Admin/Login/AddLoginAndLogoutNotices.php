<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User;

class AddLoginAndLogoutNotices implements Hookable
{
    private const META_KEY = 'user_prefers_group_url';

    public function __construct(private WpService $wpService, private AcfService $acfService)
    {
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
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
                __('Login successful', 'municipio'),
                'info',
                'save',
                [
                    'url'  => './?setPersistantGroupUrl=true',
                    'text' => __('Save this page as default home', 'municipio'),
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
                __('Login successful', 'municipio'),
                'info',
                'save',
                [
                    'url'  => './?setPersistantHomeUrl=true',
                    'text' => __('Save this page as default home', 'municipio'),
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
                self::META_KEY,
                true
            );

            $message = $result
                ? __('Option saved', 'municipio')
                : __('Option could not be saved at the moment', 'municipio');

            $type = $result ? 'info' : 'warning';
            $icon = $result ? 'check' : 'warning';

            \Municipio\Helper\Notice::add($message, $type, $icon);
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
                self::META_KEY,
            );

            $message = $result
                ? __('Option saved', 'municipio')
                : __('Option could not be saved at the moment', 'municipio');

            $type = $result ? 'info' : 'warning';
            $icon = $result ? 'check' : 'warning';

            \Municipio\Helper\Notice::add($message, $type, $icon);
        }
    }

    /**
     * Add notice when user logs in
     */
    public function addNoticeWhenUserLogsIn(): void
    {
        if ((bool)($_GET['loggedin'] ?? false) && $this->wpService->isUserLoggedIn()) {
            $currentUserGroup    = User::getCurrentUserGroup();
            $currentUserGroupUrl = User::getCurrentUserGroupUrl($currentUserGroup);
            $userPrefersGroupUrl = User::getUserPrefersGroupUrl();

            if (!$currentUserGroupUrl) {
                \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login');
                return;
            }

            if ($currentUserGroupUrl && !$userPrefersGroupUrl) {
                $this->messageWhenUserPrefersUserGroupUrl($currentUserGroup, $currentUserGroupUrl);
            } else {
                $this->messageWhenUserDoesNotPreferUserGroupUrl($currentUserGroup, $currentUserGroupUrl);
            }
        }
    }

    /**
     * Show message with user group
     */
    private function messageWhenUserPrefersUserGroupUrl(\WP_Term $currentUserGroup, string $currentUserGroupUrl): void
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
                'text' => sprintf(
                    __('Go to %s', 'municipio'),
                    $currentUserGroup->name ?? __('home', 'municipio')
                ),
            ],
            'session'
        );
    }

    /**
     * Show message without user group
     */
    private function messageWhenUserDoesNotPreferUserGroupUrl(\WP_Term $currentUserGroup, string $currentUserGroupUrl): void
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
    public function addNoticeWhenUserLogsOut(): void
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
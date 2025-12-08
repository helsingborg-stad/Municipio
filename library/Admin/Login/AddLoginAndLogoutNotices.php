<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\Helper\User\Config\UserConfig;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User\User;

/**
 * Add login and logout notices
 */
class AddLoginAndLogoutNotices implements Hookable
{
    /**
     * Constructor
     */
    public function __construct(private WpService $wpService, private AcfService $acfService, private User $userHelper, private UserConfig $userConfig)
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
        if ((bool)($_GET['offerPersistantGroupUrl'] ?? false) && $this->userHelper->canPreferGroupUrl()) {
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
        if ((bool)($_GET['offerPersistantHomeUrl'] ?? false) && $this->userHelper->canPreferGroupUrl()) {
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
        if ((bool)($_GET['setPersistantGroupUrl'] ?? false) && $this->wpService->isUserLoggedIn() && $this->userHelper->canPreferGroupUrl()) {
            $result = $this->wpService->updateUserMeta(
                $this->wpService->getCurrentUserId(),
                $this->userConfig->getUserPrefersGroupUrlMetaKey(),
                true
            );

            $message = $result
                ? $this->wpService->__('Option saved', 'municipio')
                : $this->wpService->__('Option saved', 'municipio');

            $icon = $result ? 'check_circle' : 'preliminary';

            \Municipio\Helper\Notice::add($message, 'info', $icon);
        }
    }

    /**
     * Set persistant home url
     */
    public function setPersistantHomeUrl(): void
    {
        if ((bool)($_GET['setPersistantHomeUrl'] ?? false) && $this->wpService->isUserLoggedIn() && $this->userHelper->canPreferGroupUrl()) {
            $result = $this->wpService->deleteUserMeta(
                $this->wpService->getCurrentUserId(),
                $this->userConfig->getUserPrefersGroupUrlMetaKey(),
            );

            $message = $result
                ? $this->wpService->__('Option saved', 'municipio')
                : $this->wpService->__('Option saved', 'municipio');

            $icon = $result ? 'check_circle' : 'preliminary';

            \Municipio\Helper\Notice::add($message, 'info', $icon);
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
            $userCanPreferGroupUrl   = $this->userHelper->canPreferGroupUrl();

            \Municipio\Helper\Notice::add($this->wpService->__('Login successful', 'municipio'), 'info', 'login');

            // If user cannot prefer group URL and user has a group URL, show a notice
            if (!$userCanPreferGroupUrl && $currentUserGroupUrl) {
                \Municipio\Helper\Notice::add(
                    '',
                    'info',
                    'login',
                    [
                        'url'  => $currentUserGroupUrl,
                        'text' => sprintf(
                            $this->wpService->__('Go to %s', 'municipio'),
                            $currentUserGroup->name ?? $this->wpService->__('home', 'municipio')
                        ),
                    ],
                    'session'
                );
                return;
            }

            if ($userCanPreferGroupUrl && $currentUserGroupUrl) {
                if ($userPrefersGroupUrl) {
                    $this->messageWhenUserPrefersUserGroupUrl(
                        $userPrefersGroupUrlType
                    );
                    return;
                }

                if (!$userPrefersGroupUrl) {
                    $this->messageWhenUserDoesNotPreferUserGroupUrl(
                        $currentUserGroup,
                        $currentUserGroupUrl
                    );
                    return;
                }
            }
        }
    }

    /**
     * Show message with user group
     */
    private function messageWhenUserPrefersUserGroupUrl(string $urlType): void
    {
        //Get network main site url if link type is blog_id
        if ($urlType == 'blog_id' && $this->wpService->isMultisite()) {
            $url = $this->wpService->getHomeUrl($this->wpService->getMainSiteId() ?? null);
        } else {
            $url = $this->wpService->getHomeUrl();
        }

        //Add notice
        \Municipio\Helper\Notice::add(
            '',
            'info',
            'login',
            [
                'url'  => $this->addQueryParamsToUrl(
                    $url,
                    [
                      'offerPersistantHomeUrl' => 'true'
                    ]
                ),
                'text' => $this->wpService->__('Go to default home', 'municipio'),
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
            '',
            'info',
            'login',
            [
                'url'  => $this->addQueryParamsToUrl(
                    $currentUserGroupUrl,
                    ['offerPersistantGroupUrl' => 'true']
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
}

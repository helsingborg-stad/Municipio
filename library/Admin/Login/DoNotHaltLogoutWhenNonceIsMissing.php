<?php 

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class DoNotHaltLogoutWhenNonceIsMissing implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('login_init', array($this, 'handleLogout'));
    }

    /**
     * Automatically handle logout without confirmation.
     *
     * @return void
     */
    public function handleLogout()
    {
        if (($_GET['action'] ?? null) === 'logout') {
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'log-out')) {
                wp_logout();
                wp_redirect(home_url());
                exit;
            }
        }
    }
}
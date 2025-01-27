<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class DoNotHaltAuthWhenNonceIsMissing implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->wpService->addAction('login_init', array($this, 'handleLogout'), 1);
    }

    /**
     * Check if the uunvedrfieied logout is enabled
     *
     * @return bool
     */
    private function isEnabled(): bool
    {
        return true;
    }

    /**
     * Automatically handle logout without confirmation.
     * Uses safe redirect to avoid redirecting to external sites.
     *
     * @return void
     */
    public function handleLogout()
    {
        if (($_GET['action'] ?? null) === 'logout') {
            $this->resetLogoutNonce('log-out');
        }
        if (($_GET['action'] ?? null) === 'login') {
            $this->resetLogoutNonce('login');
        }
    }

    /**
     * Reset logout nonce if it's missing.
     *
     * @param string $nonceKey
     * @return void
     */
    private function resetLogoutNonce(string $nonceKey): void
    {
        if (!$this->wpService->wpVerifyNonce(($_REQUEST['_wpnonce'] ?? ''), $nonceKey)) {
            $_REQUEST['_wpnonce'] = $this->wpService->wpCreateNonce($nonceKey);
        }
    }
}

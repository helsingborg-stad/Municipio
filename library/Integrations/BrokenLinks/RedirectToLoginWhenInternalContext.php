<?php

namespace Municipio\Integrations\BrokenLinks;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\BrokenLinks\Config\BrokenLinksConfig;
use WpOrg\Requests\Exception\Http;
use WpService\WpService;

class RedirectToLoginWhenInternalContext implements Hookable
{
    private const LOGIN_LOCK_KEY = 'municipioLoginLock';

    public function __construct(private WpService $wpService, private BrokenLinksConfig $config)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_head', [$this, 'redirectIfBrokenLink']);
        $this->wpService->addAction('wp_head', [$this, 'createLoginLockLoggedOut']);
    }

  /**
   * Redirects to login page if internal context is detected
   *
   * @return void
   */
    public function redirectIfBrokenLink()
    {
        if ($this->config->shouldRedirectToLoginPageWhenInternalContext()) {
            if (!(bool)($_GET['loggedout'] ?? false) && !$this->wpService->isUserLoggedIn()) {
                $currentUrl = $this->wpService->getPermalink(\Municipio\Helper\CurrentPostId::get());

                echo sprintf(
                    '<script>
              document.addEventListener("brokenLinkContextDetectionInternal", () => {
                const loginLockKey = "%s";
                if (!sessionStorage.getItem(loginLockKey)) {
                  window.location.href = "%s";
                }
              });
          </script>',
                    self::LOGIN_LOCK_KEY,
                    esc_js($currentUrl)
                );
            }
        }
    }

  /**
   * Create login lock when logging out
   *
   * @return void
   */
    public function createLoginLockLoggedOut()
    {
        if ((bool)($_GET['loggedout'] ?? false) && !$this->wpService->isUserLoggedIn()) {
            echo sprintf(
                '<script>
              document.addEventListener("DOMContentLoaded", function() {
                  sessionStorage.setItem("%s", "true");
              });
          </script>',
                self::LOGIN_LOCK_KEY
            );
        }
    }
}

<?php

namespace Municipio\Integrations\BrokenLinks;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\BrokenLinks\Config\BrokenLinksConfig;
use WpOrg\Requests\Exception\Http;
use WpService\WpService;

class RedirectToLoginWhenInternalContext implements Hookable
{
    private const USER_LOGGED_OUT_KEY = 'user_logged_out';
    private const USER_HAS_BEEN_AUTO_LOGGED_IN_ONCE_KEY = 'user_has_been_auto_logged_in_once';

    public function __construct(private WpService $wpService, private BrokenLinksConfig $config)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_head', [$this, 'redirectToLoginPageWhenInternalContext']);
    }

  /**
   * Redirects to login page if internal context is detected
   *
   * @return void
   */
    public function redirectToLoginPageWhenInternalContext()
    {
        if ($this->config->shouldRedirectToLoginPageWhenInternalContext()) {
            if (!(bool)($_GET['loggedout'] ?? false) && !$this->wpService->isUserLoggedIn()) {
                $loginUrl = $this->wpService->wpLoginUrl(
                    $this->getCurrentUrl(['loggedin' => 'true'])
                );

                echo sprintf(
                    '<script>
                        document.addEventListener("brokenLinkContextDetectionInternal", () => {
                          const userLoggedOutKey = "%s";
                          const userHasBeenAutoLoggedInOnceKey = "%s";
                          if (!sessionStorage.getItem(userLoggedOutKey) && !sessionStorage.getItem(userHasBeenAutoLoggedInOnceKey)) {
                            sessionStorage.setItem(userHasBeenAutoLoggedInOnceKey, "true");
                            window.location.href = "%s";
                          }
                        });
                    </script>',
                    self::USER_LOGGED_OUT_KEY,
                    self::USER_HAS_BEEN_AUTO_LOGGED_IN_ONCE_KEY,
                    esc_js($loginUrl)
                );
            }
        }
    }

    /**
     * Get current url
     * 
     * @param array $queryParam
     * 
     * @return string
     */
    private function getCurrentUrl(array $queryParam = []): string
    {
        $permalink = urldecode($this->wpService->getPermalink(\Municipio\Helper\CurrentPostId::get()));
        $permalink = add_query_arg($queryParam, $permalink);
        return urldecode($permalink);
    }
}

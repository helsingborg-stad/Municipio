<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Require SSO login for all users.
 *
 * This will automaticly redirect the user to the selected SSO provider when visiting the login page.
 * This is done simply by adding the `option=saml_user_login` parameter to the URL and reload the page.
 *
 */
class RequireSsoLogin implements Hookable
{
    private const ACTIONS_TO_EXCLUDE = ['logout', 'log-out', 'postpass'];
    private const ALLOWED_PROTOCOLS  = ['http', 'https'];

    public function __construct(private WpService $wpService, private MiniOrangeConfig $config)
    {
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', function () {
            if ($this->isEnabled()) {
                $this->redirectToSsoProvider();
            }
        });
    }

    /**
     * Check if the automatic SSO login is enabled.
     *
     * @return bool
     */
    private function isEnabled(): bool
    {
        return $this->config->requireSsoLogin();
    }

    /**
     * Redirect to the SSO provider if the current request should be redirected.
     */
    private function redirectToSsoProvider(): void
    {
        if (!$this->shouldRedirectToSso()) {
            return;
        }

        $redirectUrl = $this->getSsoUrlWithAllParams();
        $redirectUrl = $this->wpService->escUrlRaw(
            $redirectUrl,
            self::ALLOWED_PROTOCOLS
        );

        if ($redirectUrl) {
            $this->wpService->wpSafeRedirect($redirectUrl);
            exit;
        }
    }

    /*
    * Check if the current request should be redirected to the SSO provider.
    *
    * @return bool
    */
    private function shouldRedirectToSso(): bool
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? null;
        $option     = $_GET['option'] ?? null;
        $action     = $_GET['action'] ?? null;

        return $requestUri && strpos($requestUri, 'wp-login.php') !== false
            && $option !== 'saml_user_login'
            && !in_array($action, self::ACTIONS_TO_EXCLUDE, true);
    }

    /*
    * Get the URL to the SSO provider with all existing GET parameters
    * and the `option` parameter set to `saml_user_login`.
    *
    * When the login page is accessed with the `option` parameter set to `
    * saml_user_login`, it will trigger the SSO login in miniOrange plugin.
    *
    * @return string|null
    */
    private function getSsoUrlWithAllParams(): ?string
    {
        $base   = $_SERVER['REQUEST_URI'] ?? $this->wpService->homeUrl('/wp-login.php');
        $params = $_GET ?? [];

        $params['option'] = 'saml_user_login';

        return $this->wpService->addQueryArg($params, $base);
    }
}

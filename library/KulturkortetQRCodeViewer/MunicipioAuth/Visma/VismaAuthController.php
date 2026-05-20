<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\MunicipioAuthControllerInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use WpService\Contracts\AddQueryArg;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;

class VismaAuthController implements MunicipioAuthControllerInterface
{
    public static function createDefault(
        HomeUrl&AddQueryArg&WpRemoteGet&WpRemoteRetrieveBody&IsWpError $wpService,
        VismaAuthConfigInterface $config = new VismaAuthConfig(),
    ): self {
        return new self(
            new VismaApi($config, $wpService),
            new VismaAuthorizedUserFactory(),
        );
    }

    public function __construct(
        private VismaApiInterface $api,
        private VismaAuthorizedUserFactoryInterface $authorizedUserFactory = new VismaAuthorizedUserFactory(),
    ) {}

    public function validateUser(?MunicipioAuthenticatedUserInterface $user): ?MunicipioAuthenticatedUserInterface
    {
        // No additional validation needed, as we rely on Visma's session validation
        return $user && $user->getSSN() ? $user : null;
    }

    public function render(MunicipioAuthViewFactoryInterface $viewFactory, MunicipioAuthNavigationInterface $navigation): string
    {
        if ($this->api->shouldRemoteGetApiSession($navigation)) {
            return $this->handleCanGetSession($viewFactory, $navigation);
        }

        // no session, redirect to auth server
        return $this->handleHasNoSession($viewFactory, $navigation);
    }

    protected function handleHasNoSession(MunicipioAuthViewFactoryInterface $viewFactory, MunicipioAuthNavigationInterface $navigation): string
    {
        try {
            $redirectUrl = $this->api->remoteApiLogin($navigation);
            if ($redirectUrl) {
                return $viewFactory->whenAnonymous($redirectUrl, $navigation);
            }
            throw new \Exception('Failed to get redirect URL from Visma');
        } catch (\Exception $e) {
            return $viewFactory->whenError($e->getMessage(), $navigation);
        }
    }

    protected function handleCanGetSession(MunicipioAuthViewFactoryInterface $viewFactory, MunicipioAuthNavigationInterface $navigation): string
    {
        try {
            $session = $this->api->remoteApiGetSession($navigation);
            if ($session) {
                $user = $this->validateUser($this->authorizedUserFactory->createAuthorizedUser($session));
                if (!$user) {
                    return $viewFactory->whenAnonymous($navigation->getHomeUrl(), $navigation);
                }
                return $viewFactory->whenAuthenticated($user, $navigation);
            }
            throw new \Exception('Invalid session');
        } catch (\Exception $e) {
            return $viewFactory->whenError($e->getMessage(), $navigation);
        }
    }
}

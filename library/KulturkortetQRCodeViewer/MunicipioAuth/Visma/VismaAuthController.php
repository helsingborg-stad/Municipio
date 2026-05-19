<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\MunicipioAuthControllerInterface;
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
            new VismaContext($config, $wpService),
            new VismaApi($config, new VismaContext($config, $wpService), $wpService),
            new VismaAuthorizedUserFactory(),
        );
    }

    public function __construct(
        private VismaContextInterface $context,
        private VismaApiInterface $api,
        private VismaAuthorizedUserFactoryInterface $authorizedUserFactory = new VismaAuthorizedUserFactory(),
    ) {}

    public function getHomeUrl(): string
    {
        return $this->context->getHomeUrl();
    }

    public function render(MunicipioAuthViewFactoryInterface $viewFactory): string
    {
        if ($this->api->shouldRemoteGetApiSession()) {
            return $this->handleCanGetSession($viewFactory);
        }

        // no session, redirect to auth server
        return $this->handleHasNoSession($viewFactory);
    }

    protected function handleHasNoSession(MunicipioAuthViewFactoryInterface $viewFactory): string
    {
        try {
            $redirectUrl = $this->api->remoteApiLogin();
            if ($redirectUrl) {
                return $viewFactory->whenAnonymous($redirectUrl);
            }
            throw new \Exception('Failed to get redirect URL from Visma');
        } catch (\Exception $e) {
            return $viewFactory->whenError($e->getMessage(), $this->context->getHomeUrl());
        }
    }

    protected function handleCanGetSession(MunicipioAuthViewFactoryInterface $viewFactory): string
    {
        try {
            $session = $this->api->remoteApiGetSession();
            if ($session) {
                return $viewFactory->whenAuthenticated($this->authorizedUserFactory->createAuthorizedUser($session));
            }
            throw new \Exception('Invalid session');
        } catch (\Exception $e) {
            return $viewFactory->whenError($e->getMessage(), $this->context->getHomeUrl());
        }
    }
}

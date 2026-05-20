<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\secure;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\MunicipioAuthControllerInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUser;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use Override;

class SecureMunicipioAuthController implements MunicipioAuthControllerInterface
{
    public static function createDefault(
        MunicipioAuthControllerInterface $inner,
        SecureMunicipioAuthConfigInterface $config,
    ): self {
        return new self(
            $inner,
            $config,
            new CookieStrategy(),
            new JWTStrategy(),
        );
    }

    public function __construct(
        private MunicipioAuthControllerInterface $inner,
        private SecureMunicipioAuthConfigInterface $config,
        private CookieStrategyInterface $cookieStrategy = new CookieStrategy(),
        private JWTStrategyInterface $jwtStrategy = new JWTStrategy(),
    ) {}

    #[Override]
    public function validateUser(?MunicipioAuthenticatedUserInterface $user): ?MunicipioAuthenticatedUserInterface
    {
        return $this->inner->validateUser($user);
    }

    public function getHomeUrl(): string
    {
        return $this->inner->getHomeUrl();
    }

    public function render(MunicipioAuthViewFactoryInterface $viewFactory): string
    {
        try {
            $secureViewFactory = new class(
                $this,
                $viewFactory,
            ) implements MunicipioAuthViewFactoryInterface {
                public function __construct(
                    private SecureMunicipioAuthController $controller,
                    private MunicipioAuthViewFactoryInterface $viewFactory,
                ) {}

                public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user): string
                {
                    $validateUser = $this->controller->validateUser($user);
                    $this->controller->trySetUserCookie($validateUser);
                    return $validateUser ? $this->viewFactory->whenAuthenticated($validateUser) : $this->viewFactory->whenAnonymous($this->controller->getHomeUrl());
                }

                public function whenAnonymous(string $redirectUrl): string
                {
                    return $this->viewFactory->whenAnonymous($redirectUrl);
                }

                public function whenError(string $errorMessage, string $homeUrl): string
                {
                    return $this->viewFactory->whenError($errorMessage, $homeUrl);
                }
            };

            $user = $this->validateUser($this->tryGetUserFromCookieJWT());
            if ($user) {
                return $viewFactory->whenAuthenticated($user);
            }

            return $this->inner->render($secureViewFactory);
        } catch (\Exception $e) {
            return $viewFactory->whenError($e->getMessage(), $this->getHomeUrl());
        }
    }

    protected function tryUnpackUser(array $payload): ?MunicipioAuthenticatedUserInterface
    {
        return $this->validateUser(new MunicipioAuthenticatedUser(
            $payload['ssn'] ?? '',
            $payload['cn'] ?? '',
            $payload['gn'] ?? '',
            $payload['sn'] ?? '',
        ));
    }

    protected function tryPackUser(?MunicipioAuthenticatedUserInterface $user): ?array
    {
        return (
            $user
                ? [
                    'ssn' => $user->getSSN(),
                    'cn' => $user->getName(),
                    'gn' => $user->getFirstName(),
                    'sn' => $user->getLastName(),
                ] : null
        );
    }

    protected function tryGetUserFromCookieJWT(): ?MunicipioAuthenticatedUserInterface
    {
        $cookieValue = $this->cookieStrategy->getCookie($this->config);
        if (!$cookieValue) {
            return null;
        }

        $decoded = $this->jwtStrategy->tryDecode($cookieValue, $this->config);
        if (!$decoded) {
            return null;
        }

        return $this->tryUnpackUser($decoded);
    }

    public function trySetUserCookie(?MunicipioAuthenticatedUserInterface $user): void
    {
        $payload = $this->tryPackUser($user);
        $jwt = $payload ? $this->jwtStrategy->encode($payload, $this->config) : '';
        $this->cookieStrategy->setCookie($jwt, $this->config);
    }
}

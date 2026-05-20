<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

use Municipio\Kulturkortet\MunicipioAuth\controller\MunicipioAuthControllerInterface;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUser;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\Kulturkortet\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;

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

    public function validateUser(?MunicipioAuthenticatedUserInterface $user): ?MunicipioAuthenticatedUserInterface
    {
        return $this->inner->validateUser($user);
    }

    public function tryLogoutUser(?MunicipioAuthenticatedUserInterface $user): void
    {
        $this->trySetUserCookie(null);
        $this->inner->tryLogoutUser($user);
    }

    public function render(MunicipioAuthViewFactoryInterface $viewFactory, MunicipioAuthNavigationInterface $navigation): string
    {
        try {
            $secureViewFactory = $this->decorateViewFactory($viewFactory);

            $user = $this->validateUser($this->tryGetUserFromCookieJWT());

            if ($navigation->getQueryParameter('action') === 'logout') {
                if ($user) {
                    return $secureViewFactory->whenLogOut($user, $navigation);
                }
                $navigation->redirect($navigation->getModifiedHomeUrl(removeQueryArgs: ['action']));
            }

            if ($user) {
                // bypass our decorator to avoid setting cookie again and potentially causing issues with logout flow
                return $viewFactory->whenAuthenticated($user, $navigation);
            }

            return $this->inner->render($secureViewFactory, $navigation);
        } catch (\Exception $e) {
            return $viewFactory->whenError($e->getMessage(), $navigation);
        }
    }

    protected function tryUnpackUser(array $payload): ?MunicipioAuthenticatedUserInterface
    {
        return $this->validateUser(new MunicipioAuthenticatedUser(
            $payload['psessid'] ?? null,
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
                    'psessid' => $user->getProviderSessionId(),
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

    private function decorateViewFactory(MunicipioAuthViewFactoryInterface $viewFactory): MunicipioAuthViewFactoryInterface
    {
        return new class($this, $viewFactory) implements MunicipioAuthViewFactoryInterface {
            public function __construct(
                private SecureMunicipioAuthController $controller,
                private MunicipioAuthViewFactoryInterface $inner,
            ) {}

            public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string
            {
                $validateUser = $this->controller->validateUser($user);
                $this->controller->trySetUserCookie($validateUser);
                return $validateUser ? $this->inner->whenAuthenticated($validateUser, $navigation) : $this->inner->whenAnonymous($navigation->getHomeUrl(), $navigation);
            }

            public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string
            {
                return $this->inner->whenAnonymous($loginUrl, $navigation);
            }

            public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string
            {
                $this->controller->tryLogoutUser($user);
                return $this->inner->whenLogOut($user, $navigation);
            }

            public function whenError(string $error, MunicipioAuthNavigationInterface $navigation): string
            {
                return $this->inner->whenError($error, $navigation);
            }
        };
    }
}

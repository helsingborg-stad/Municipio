<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemotePost;
use WpService\Contracts\WpRemoteRetrieveBody;

class VismaApi implements VismaApiInterface
{
    public function __construct(
        private VismaAuthConfigInterface $config,
        private WpRemoteGet&WpRemotePost&WpRemoteRetrieveBody&IsWpError $wpService,
    ) {}

    public function shouldRemoteGetApiSession(MunicipioAuthNavigationInterface $navigation): bool
    {
        return $navigation->getQueryParameter('ts_session_id') !== null;
    }

    public function remoteApiLogin(MunicipioAuthNavigationInterface $navigation): ?string
    {
        $body = $this->remoteGetJson('/json1.1/Login', ['callbackUrl' => $navigation->getModifiedHomeUrl(removeQueryArgs: ['ts_session_id'])]);
        if ($body && isset($body['redirectUrl'])) {
            return $body['redirectUrl'];
        }
        return null;
    }

    public function remoteApiGetSession(MunicipioAuthNavigationInterface $navigation): ?array
    {
        $body = $this->remoteGetJson('/json1.1/GetSession', ['sessionId' => $navigation->getQueryParameter('ts_session_id')]);
        return $body ?? null;
    }

    public function remoteApiLogout(MunicipioAuthenticatedUserInterface $user): void
    {
        $this->remotePostJson('/json1.1/Logout', ['sessionId' => $user->getProviderSessionId()]);
    }

    protected function remoteGetJson(string $path, array $queryParams = []): array
    {
        $url = $this->makeVismaUrl($path, [
            ...$queryParams,
            'customerKey' => $this->config->getCustomerKey(),
            'serviceKey' => $this->config->getServiceKey(),
        ]);
        $response = $this->wpService->wpRemoteGet($url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        if ($this->wpService->isWpError($response)) {
            throw new \Exception('Error during Visma API request: ' . $response->get_error_message());
        }
        $rawBody = $this->wpService->wpRemoteRetrieveBody($response);
        $body = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error parsing JSON response from Visma API: ' . json_last_error_msg() . ' - Response was: ' . $rawBody);
        }
        return $body;
    }

    protected function remotePostJson(string $path, array $queryParams = []): ?array
    {
        $url = $this->makeVismaUrl($path, [
            ...$queryParams,
            'customerKey' => $this->config->getCustomerKey(),
            'serviceKey' => $this->config->getServiceKey(),
        ]);
        $response = $this->wpService->wpRemotePost($url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        if ($this->wpService->isWpError($response)) {
            throw new \Exception('Error during Visma API request: ' . $response->get_error_message());
        }
        $rawBody = $this->wpService->wpRemoteRetrieveBody($response);

        if ($rawBody) {
            $body = json_decode($rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error parsing JSON response from Visma API: ' . json_last_error_msg() . ' - Response was: ' . $rawBody);
            }
            return $body;
        }
        return null;
    }

    protected function makeVismaUrl(string $path, array $queryParams = []): string
    {
        return $this->config->isValid() ? $this->config->getBaseUrl() . $path . '?' . http_build_query($queryParams) : '';
    }
}

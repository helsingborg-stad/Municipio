<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\navigation;

use WpService\Contracts\AddQueryArg;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\RemoveQueryArg;

class MunicipioAuthNavigation implements MunicipioAuthNavigationInterface
{
    public function __construct(
        private HomeUrl&AddQueryArg&RemoveQueryArg $wpService,
        private ?string $homeUrl = null,
    ) {}

    public function getHomeUrl(): string
    {
        return $this->homeUrl ?? $this->wpService->homeUrl($this->wpService->addQueryArg($_GET));
    }

    public function getModifiedHomeUrl(array $removeQueryArgs = [], array $addQueryArgs = []): string
    {
        $currentUrl = $this->getHomeUrl();

        // Remove specified query args
        foreach ($removeQueryArgs as $arg) {
            $currentUrl = $this->wpService->removeQueryArg($arg, $currentUrl);
        }

        // Add new query args
        if (!empty($addQueryArgs)) {
            $currentUrl = $this->wpService->addQueryArg($addQueryArgs, $currentUrl);
        }

        return $currentUrl;
    }

    public function getQueryParameter(string $name): ?string
    {
        // We need to parse the query parameters manually here since we want to avoid relying on WordPress's global $_GET
        $query = parse_url($this->getHomeUrl(), PHP_URL_QUERY) ?? '';
        $queryParams = [];
        parse_str($query, $queryParams);
        return $queryParams[$name] ?? null;
    }

    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }
}

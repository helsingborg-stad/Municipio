<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

use WpService\Contracts\AddQueryArg;
use WpService\Contracts\HomeUrl;

class VismaContext implements VismaContextInterface
{
    public function __construct(
        private readonly VismaAuthConfigInterface $config,
        private readonly HomeUrl&AddQueryArg $wpService,
        private readonly ?string $homeUrl = null,
    ) {}

    public function getHomeUrl(): string
    {
        global $wp;
        return $this->homeUrl ?? $this->wpService->homeUrl($this->wpService->addQueryArg($_GET), $wp->request);
    }

    public function shouldRemoteGetApiSession(): bool
    {
        return $this->getQueryParameter('ts_session_id') !== null;
    }

    public function getQueryParameter(string $name): ?string
    {
        // We need to parse the query parameters manually here since we want to avoid relying on WordPress's global $_GET
        $query = parse_url($this->getHomeUrl(), PHP_URL_QUERY) ?? '';
        $queryParams = [];
        parse_str($query, $queryParams);
        return $queryParams[$name] ?? null;
    }
}

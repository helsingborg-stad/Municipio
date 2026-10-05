<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

use WpService\Contracts\HomeUrl;

final class CurrentUrl implements CurrentUrlInterface
{
    public function __construct(private HomeUrl $wpService) {}

    public function get(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $requestPath = parse_url(is_string($requestUri) ? $requestUri : '/', PHP_URL_PATH);
        $requestPath = is_string($requestPath) ? $requestPath : '';

        $homeUrl = $this->wpService->homeUrl();
        $homePath = parse_url($homeUrl, PHP_URL_PATH);
        $homePath = is_string($homePath) ? rtrim($homePath, '/') : '';

        if ($homePath !== '' && str_starts_with($requestPath, $homePath . '/')) {
            $requestPath = substr($requestPath, strlen($homePath));
        }

        return $this->wpService->homeUrl(ltrim($requestPath, '/'));
    }
}

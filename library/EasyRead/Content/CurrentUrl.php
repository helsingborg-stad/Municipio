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
        $path = parse_url(is_string($requestUri) ? $requestUri : '/', PHP_URL_PATH);
        return $this->wpService->homeUrl(is_string($path) ? $path : '');
    }
}

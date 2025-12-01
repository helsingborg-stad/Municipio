<?php

namespace Municipio\Controller\SingularEvent\EnsureVisitingSingularOccasion\Redirect;

use WpService\Contracts\WpRedirect;

class Redirect implements RedirectInterface
{
    public function __construct(private WpRedirect $wpService)
    {
    }

    public function redirect(string $url): void
    {
        $this->wpService->wpRedirect($url);
        exit;
    }
}

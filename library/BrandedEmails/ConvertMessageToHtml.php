<?php

namespace Municipio\BrandedEmails;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\Wpautop;

class ConvertMessageToHtml implements Hookable
{
    public function __construct(private AddFilter&Wpautop $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_mail', [$this, 'convertMessageToHtml']);
    }

    public function convertMessageToHtml(array $args): array
    {
        $args['message'] = $this->wpService->wpautop($args['message']);
        return $args;
    }
}

<?php

namespace Municipio\BrandedEmails;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\Autop;

class ConvertMessageToHtml implements Hookable
{
    public function __construct(private AddFilter&Autop $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_mail', [$this, 'convertMessageToHtml']);
    }

    public function convertMessageToHtml(array $args): array
    {
        $args['message'] = $this->wpService->autop($args['message']);
        return $args;
    }
}

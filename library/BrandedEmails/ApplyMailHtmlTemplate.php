<?php

namespace Municipio\BrandedEmails;

use Municipio\BrandedEmails\HtmlTemplate\HtmlTemplate;
use WpService\Contracts\AddFilter;

class ApplyMailHtmlTemplate implements Hookable
{
    public function __construct(private HtmlTemplate $template, private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_mail', [$this, 'apply']);
    }

    public function apply(array $args): array
    {
        $message = $args['message'];
        $message = $this->template->getHeader() . $message . $this->template->getFooter();

        $args['message'] = $message;

        return $args;
    }
}

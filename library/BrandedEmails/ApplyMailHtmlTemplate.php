<?php

namespace Municipio\BrandedEmails;

use Municipio\BrandedEmails\HtmlTemplate\HtmlTemplate;
use Municipio\HooksRegistrar\Hookable;
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
        $this->template->setSubject($args['subject'] ?? '');
        $this->template->setContent($args['message'] ?? '');

        $args['message'] = $this->template->getHtml();

        return $args;
    }
}

<?php

namespace Municipio\BrandedEmails\HtmlTemplate;

use HelsingborgStad\BladeService\BladeServiceInterface;
use Municipio\BrandedEmails\HtmlTemplate\Config\HtmlTemplateConfig;
use WpService\Contracts\__;
use WpService\Contracts\GetBloginfo;

class DefaultHtmlTemplate implements HtmlTemplate
{
    private string $subject;
    private string $content;

    public function __construct(
        private HtmlTemplateConfig $config,
        private GetBloginfo&__ $wpService,
        private BladeServiceInterface $bladeService
    ) {
    }

    public function setSubject(string $content): void
    {
        $this->subject = $content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getHtml(): string
    {
        return $this->bladeService->makeView('default', [
            'content'    => $this->content,
            'subject'    => $this->subject,
            'footerText' => $this->getFooterText(),
            'logoSrc'    => $this->config->getLogoSrc(),
            'styles'     => [
                'headerBackgroundColor' => $this->config->getHeaderBackgroundColor(),
                'textColor'             => $this->config->getTextColor(),
                'backgroundColor'       => $this->config->getBackgroundColor(),
                'footerBackgroundColor' => $this->config->getFooterBackgroundColor(),
                'footerTextColor'       => $this->config->getFooterTextColor(),
            ],
        ])->render();
    }

    private function getFooterText(): string
    {
        $websiteName = $this->wpService->getBloginfo('name');
        $websiteUrl  = $this->wpService->getBloginfo('url');
        $link        = "<a href='{$websiteUrl}'>{$websiteName}</a>";
        return sprintf($this->wpService->__('This email was sent from %s.', 'municipio'), $link);
    }
}

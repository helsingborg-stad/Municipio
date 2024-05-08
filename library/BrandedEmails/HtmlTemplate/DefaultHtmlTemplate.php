<?php

namespace Municipio\BrandedEmails\HtmlTemplate;

use Municipio\BrandedEmails\HtmlTemplate\Config\HtmlTemplateConfig;
use WpService\Contracts\__;
use WpService\Contracts\GetBloginfo;

class DefaultHtmlTemplate implements HtmlTemplate
{
    public function __construct(private HtmlTemplateConfig $config, private GetBloginfo&__ $wpService)
    {
    }

    public function getHeader(): string
    {
        extract([
            'headerBackgroundColor' => $this->config->getHeaderBackgroundColor(),
            'logoSrc'               => $this->config->getLogoSrc(),
            'textColor'             => $this->config->getTextColor(),
            'backgroundColor'       => $this->config->getBackgroundColor(),
            'footerBackgroundColor' => $this->config->getFooterBackgroundColor(),
            'footerTextColor'       => $this->config->getFooterTextColor()
        ]);

        ob_start();
        include __DIR__ . '/views/Default.Header.php';
        return ob_get_clean();
    }

    public function getFooter(): string
    {
        extract([
            'footerText' => $this->getFooterText()
        ]);

        ob_start();
        include __DIR__ . '/views/Default.Footer.php';
        return ob_get_clean();
    }

    private function getFooterText(): string
    {
        $websiteName = $this->wpService->getBloginfo('name');
        $websiteUrl  = $this->wpService->getBloginfo('url');
        $link        = "<a href='{$websiteUrl}'>{$websiteName}</a>";
        return sprintf($this->wpService->__('This email was sent from %s.', 'municipio'), $link);
    }
}

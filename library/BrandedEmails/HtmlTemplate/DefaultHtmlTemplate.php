<?php

namespace Municipio\BrandedEmails\HtmlTemplate;

class DefaultHtmlTemplate implements HtmlTemplate
{
    public function getHeader(): string
    {
        $html  = '<html>';
        $html .= '<head>';
        $html .= '<style>';
        $html .= '/* CSS styles for the email header */';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<header>';
        $html .= '<h1>Welcome to our newsletter!</h1>';
        $html .= '</header>';

        return $html;
    }

    public function getFooter(): string
    {
        $html  = '</body>';
        $html .= '</html>';

        return $html;
    }
}

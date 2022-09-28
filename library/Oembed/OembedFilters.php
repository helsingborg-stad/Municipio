<?php

namespace Municipio\Oembed;

class OembedFilters
{
    public function __construct()
    {
        add_filter('oembed_result', '\Municipio\Oembed\OembedFilters::oembed_result', 99, 2);
    }

    public static function oembed_result($html, $data)
    {
        $output = '';

        if (str_contains($html, '<iframe')) {
            $doc = new \DOMDocument();
            $doc->loadHTML($html);

            $iframes = $doc->getElementsByTagName('iframe');

            foreach ($iframes as $iframe) {
                $src = $iframe->getAttribute('src');
                $iframe->setAttribute('src', 'about:blank');
                $iframe->setAttribute('data-src', $src);

                $classes = $iframe->getAttribute('class');
                $classes .= ' js-suppressed-iframe';
                $iframe->setAttribute('class', $classes);
            }

            $output = $doc->saveHTML();
        }

        return $output;
    }
}

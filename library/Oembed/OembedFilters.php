<?php

namespace Municipio\Oembed;

class OembedFilters
{
    public function __construct()
    {
        add_filter('oembed_result', '\Municipio\Oembed\OembedFilters::oembed_result', 99, 2);
    }

    public static function oembed_result($data, $url)
    {
        if (str_contains($data, '<iframe')) {
            $doc = new \DOMDocument();
            $doc->loadHTML($data);

            $iframes = $doc->getElementsByTagName('iframe');

            foreach ($iframes as $iframe) {
                $src = $iframe->getAttribute('src');
                $iframe->setAttribute('data-src', $src);
            }

            $data = $doc->saveHTML();
        }

        return $data;
    }
}

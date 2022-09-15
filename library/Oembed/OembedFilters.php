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
            $dom = new \DOMDocument();
            $dom->loadHTML($data, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $xpath = new \DOMXPath($dom);
            $iframe = $xpath->query("//iframe");

            if ((bool) $iframe) {
                foreach ($iframe as $item) {
                    $src         = $item->getAttribute('src');
                    $item->setAttribute('src', 'about:blank');
                    $item->setAttribute('data-src', $src);
                }
                $data = $dom->saveXML();
            }
        }

        return $data;
    }
}

<?php

namespace Municipio\Oembed;

class OembedFilters
{
    public function __construct()
    {

		add_filter('oembed_result', '\Municipio\Oembed\OembedFilters::oembed_result', 99, 2);

    }

	static public function oembed_result( $data, $url ) {	

		if ( str_contains( $data, '<iframe' ) ) {

			$dom = new \DOMDocument();
			$dom->loadHTML($data, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
			$xpath = new \DOMXPath($dom);

			if( (bool) $xpath->query("//iframe") ) {

				$src         = $xpath->query("//iframe")[0]->getAttribute('src');
				$width       = $xpath->query("//iframe")[0]->getAttribute('width');
				$height      = $xpath->query("//iframe")[0]->getAttribute('height');
				$frameborder = $xpath->query("//iframe")[0]->getAttribute('frameborder');

				$data = "<iframe src='about:blank' data-src='{$src}' width='{$width}' height='{$height}' frameborder='{$frameborder}'></iframe>";

			}

		}

		return $data;
	}
}

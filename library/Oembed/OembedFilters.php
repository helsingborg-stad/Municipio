<?php

namespace Municipio\Oembed;

class OembedFilters
{
    public function __construct()
    {
        add_filter('oembed_dataparse', '\Municipio\Oembed\OembedFilters::oembed_dataparse', 99, 3);
    }

    public static function oembed_dataparse($output, $data, $url)
    {
        $output = render_blade_view('partials.iframe', ['data' => $data, 'src' => $url]);

        return $output;
    }
}

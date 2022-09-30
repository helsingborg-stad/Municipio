<?php

namespace Municipio\Oembed;

class OembedFilters
{
    public function __construct()
    {
        add_filter('oembed_dataparse', '\Municipio\Oembed\OembedFilters::oembed_dataparse', 1, 3);
    }


    public static function oembed_dataparse($output, $data, $url)
    {
        if (str_contains($output, '<iframe')) {
            $data->lang = (object) [
                'knownLabels' => [
                    'title' => __('We need your consent to continue', 'modularity'),
                    'info' => sprintf(__('This part of the website shows content from %s. By continuing, <a href="%s"> you are accepting GDPR and privacy policy</a>.', 'modularity'), '{SUPPLIER_WEBSITE}', '{SUPPLIER_POLICY}'),
                    'button' => __('I understand, continue.', 'modularity'),
                ],

                'unknownLabels' => [
                    'title' => __('We need your consent to continue', 'modularity'),
                    'info' => __('This part of the website shows content from another website. By continuing, you are accepting GDPR and privacy policy.', 'modularity'),
                    'button' => __('I understand, continue.', 'modularity'),
                ],
            ];

            return render_blade_view('partials.iframe', ['data' => $data, 'src' => $url]);
        }
        return $output;
    }
}

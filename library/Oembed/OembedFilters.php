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
                     'title'  => __('We need your consent to continue', 'municipio'),
                     'info'   => sprintf(__('This part of the website shows content from %s. By continuing, <a href="%s"> you are accepting GDPR and privacy policy</a>.', 'municipio'), '{SUPPLIER_WEBSITE}', '{SUPPLIER_POLICY}'),
                     'button' => __('I understand, continue.', 'municipio'),
                ],

                'unknownLabels' => [
                     'title'  => __('We need your consent to continue', 'municipio'),
                     'info'   => sprintf(__('This part of the website shows content from another website (%s). By continuing, you are accepting GDPR and privacy policy.', 'municipio'), '{SUPPLIER_WEBSITE}'),
                     'button' => __('I understand, continue.', 'municipio'),
                ],

                'infoLabel' => __('Handling of personal data', 'municipio'),
            ];

            $videoService = new \Municipio\Helper\VideoService($url);
            $coverArt = $videoService->getCoverArt();
            
            return render_blade_view(
                'partials.iframe',
                [
                    'settings' => $data,
                    'src'      => $url,
                    'poster'   => $coverArt,
                ]
            );
        }
        return $output;
    }
}

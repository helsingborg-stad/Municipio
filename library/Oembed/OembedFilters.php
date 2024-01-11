<?php

namespace Municipio\Oembed;

/**
 * Class OembedFilters
 *
 * This class provides filters for oEmbed data parsing.
 */
class OembedFilters
{
    /**
     * OembedFilters constructor.
     *
     * Initializes the class and adds the oembed_dataparse filter.
     */
    public function __construct()
    {
        add_filter('oembed_dataparse', '\Municipio\Oembed\OembedFilters::oembedDataparse', 1, 3);
    }

    /**
     * Callback function for the oembed_dataparse filter.
     *
     * Modifies the oEmbed output if it contains an iframe.
     *
     * @param string $output The oEmbed output.
     * @param object $data The oEmbed data object.
     * @param string $url The oEmbed URL.
     * @return string The modified oEmbed output.
     */
    public static function oembedDataparse($output, $data, $url)
    {
        if (str_contains($output, '<iframe')) {
            $data->lang = [
                'knownLabels'   => [
                     'title'  => __('We need your consent to continue', 'municipio'),
                     'info'   => sprintf(__('This part of the website shows content from %s. By continuing, <a href="%s"> you are accepting GDPR and privacy policy</a>.', 'municipio'), '{SUPPLIER_WEBSITE}', '{SUPPLIER_POLICY}'),
                     'button' => __('I understand, continue.', 'municipio'),
                ],

                'unknownLabels' => [
                     'title'  => __('We need your consent to continue', 'municipio'),
                     'info'   => sprintf(__('This part of the website shows content from another website (%s). By continuing, you are accepting GDPR and privacy policy.', 'municipio'), '{SUPPLIER_WEBSITE}'),
                     'button' => __('I understand, continue.', 'municipio'),
                ],

                'infoLabel'     => __('Handling of personal data', 'municipio'),
            ];

            $videoService = new \Municipio\Helper\VideoService($url);
            $poster       = $videoService->getCoverArt();

            return render_blade_view(
                'partials.iframe',
                [
                    'settings' => $data,
                    'src'      => $url,
                    'poster'   => $poster,
                ]
            );
        }
        return $output;
    }
}

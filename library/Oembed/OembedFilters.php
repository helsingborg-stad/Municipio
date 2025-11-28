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
        add_filter('oembed_dataparse', [$this,'oembedDataparse'], 1, 3);
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
    public function oembedDataparse($output, $data, $url)
    {
        $url = $this->buildEmbedUrl($url);

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
    /**
     * Build embed url
     *
     * @param string    $src    Arbitrary embed url
     * @return string   $src    Correct embed url
     */
    protected function buildEmbedUrl($src)
    {
        $srcParsed = parse_url($src);

        $ytParams = 'autoplay=1&showinfo=0&rel=0&mute=1&modestbranding=1&cc_load_policy=1';

        switch ($srcParsed['host']) {
            case 'youtube.com':
            case 'www.youtube.com':
                /*
                Replacing the path with /embed/ and then
                adding the v query parameter to the path
                and removing the v parameter from the
                query string.
                */
                $srcParsed['host'] = 'www.youtube.com';

                if (isset($srcParsed['query'])) {
                    parse_str($srcParsed['query'], $query);
                    if (isset($query['v'])) {
                        $srcParsed['path']  = '/embed/' . $query['v'];
                        $srcParsed['query'] = $ytParams;
                    }
                }
                break;
            case 'youtu.be':
                $srcParsed['host'] = 'youtube.com';
                if (isset($srcParsed['path'])) {
                    $srcParsed['path']  = '/embed' . $srcParsed['path'];
                    $srcParsed['query'] = $ytParams;
                }
                break;
            case 'vimeo.com':
            case 'www.vimeo.com':
                $srcParsed['host'] = 'player.vimeo.com';
                if (isset($srcParsed['path'])) {
                    $srcParsed['path'] = '/video' . $srcParsed['path'] . "?autoplay=1&autopause=0&muted=1";
                }
                break;
            case 'spotify.com':
            case 'www.spotify.com':
            case 'open.spotify.com':
                $srcParsed['host']  = 'open.spotify.com';
                $srcParsed['query'] = 'utm_source=oembed';
                break;
            case 'soundcloud.com':
            case 'www.soundcloud.com':
                $response = wp_remote_get("https://soundcloud.com/oembed?format=json&url={$src}");

                if (is_wp_error($response)) {
                    return $src;
                }

                $apiResponse = json_decode(wp_remote_retrieve_body($response), true);

                $iframeSrc = $this->extractIframeSrc($apiResponse['html'], $srcParsed);
                $srcParsed = parse_url($iframeSrc);

                break;
            default:
                break;
        }

        $scheme   = $srcParsed['scheme'] ?? 'https';
        $embedUrl = $scheme . '://' . strtolower($srcParsed['host']);

        if (isset($srcParsed['path'])) {
            $embedUrl .= $srcParsed['path'];
        }
        if (isset($srcParsed['query'])) {
            $embedUrl .= '?' . $srcParsed['query'];
        }

        return $embedUrl;
    }
    /**
     * Extracts the source URL from an iframe element in the given HTML.
     *
     * @param string $html The HTML containing the iframe element.
     * @return string The source URL of the iframe.
     */
    private function extractIframeSrc(string $html)
    {
        $dom = new \DOMDocument();
        // Suppress errors due to malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_NOERROR);
        // Clear errors
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        $src   = $xpath->evaluate("string(//iframe/@src)");

        return $src;
    }
}

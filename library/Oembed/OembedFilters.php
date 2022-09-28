<?php

namespace Municipio\Oembed;

class OembedFilters
{
	public function __construct()
	{

        // Filters
        add_filter('embed_oembed_html', '\Municipio\Oembed\OembedFilters::embed_oembed_html', 10, 4); // Enables oembed 

		add_filter('oembed_dataparse', '\Municipio\Oembed\OembedFilters::oembed_dataparse', 1, 3);
	}


    /**
     * Filters oembed output
     * @param  string $data Markup
     * @param  string $url  Embedded url
     * @param  array $args  Args
     * @return string       Markup
     */
    public static function embed_oembed_html($html, $url, $attr, $postId, $wrapper = true)
    {
        $provider = false;

        if (strpos(strtolower($url), 'youtube') !== false || strpos(strtolower($url), 'youtu.be') !== false) {
            $provider = 'YouTube';
        } elseif (strpos(strtolower($url), 'vimeo') !== false) {
            $provider = 'Vimeo';
        }

        $shouldFilter = apply_filters('Municipio/oembed/should_filter_markup', true, $provider, $url, $postId);

        // Check if there's a oembed class for the provider
        if (!class_exists('\Municipio\Oembed\\' . $provider) || !$shouldFilter) {
            return $html;
        }

        $class = '\Municipio\Oembed\\' . $provider;
        $oembed = new $class($url, $html, $wrapper);

        return $oembed->output();
    }


	public static function oembed_dataparse($output, $data, $url)
	{
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
		
		$output = render_blade_view('partials.iframe', ['data' => $data, 'src' => $url]);

		return $output;
	}
}

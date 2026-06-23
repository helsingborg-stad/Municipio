<?php

namespace Municipio\Customizer\Controls;

use Municipio\Helper\CacheBust;

class CustomizerControlAssets
{
    /**
     * Enqueue the shared Customizer control script bundle.
     *
     * @return void
     */
    public static function enqueueScript(): void
    {
        wp_enqueue_style('dashicons');

        wp_enqueue_script(
            'municipio-customizer-controls',
            get_template_directory_uri() . '/assets/dist/' . CacheBust::name('js/customizer-controls.js'),
            ['customize-controls', 'jquery-ui-sortable', 'wp-color-picker', 'wp-i18n'],
            null,
            true,
        );
    }
}

<?php

$municipio_intranet_walkthrough_counter = 0;

if (!function_exists('municipio_intranet_walkthrough')) {
    /**
     * Formats a site's name correctly from a site array
     * @param  array $site The site to format name for
     * @return string
     */
    function municipio_intranet_walkthrough($title, $html)
    {
        if (!isset($_GET['walkthrough'])) {
            return;
        }

        global $municipio_intranet_walkthrough_counter;
        $municipio_intranet_walkthrough_counter++;

        return '
            <div class="walk-through" data-step="' . $municipio_intranet_walkthrough_counter . '">
                <div class="blipper" data-dropdown=".blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown"></div>
                <div class="dropdown-menu dropdown-menu-arrow blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown gutter">
                    <h4>' . $title . '</h4>
                    <p>
                        ' . $html . '
                    </p>
                </div>
            </div>
        ';
    }
}

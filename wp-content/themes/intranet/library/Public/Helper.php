<?php

$municipio_intranet_walkthrough_counter = 0;

if (!function_exists('municipio_intranet_walkthrough')) {
    /**
     * Formats a site's name correctly from a site array
     * @param  array $site The site to format name for
     * @return string
     */
    function municipio_intranet_walkthrough($title, $html, $highlightSelector = null, $position = 'left')
    {
        if (!isset($_GET['walkthrough'])) {
            return;
        }

        global $municipio_intranet_walkthrough_counter;
        $municipio_intranet_walkthrough_counter++;

        if ($highlightSelector) {
            $highlightSelector = ' data-highlight="' . $highlightSelector . '"';
        }

        switch ($position) {
            case 'center':
                $position = 'walkthrough-center';
                break;

            case 'right':
                $position = 'walkthrough-right';
                break;

            default:
                $position = 'walkthrough-left';
                break;
        }

        return '
            <div class="walkthrough ' . $position . '" data-step="' . $municipio_intranet_walkthrough_counter . '"' . $highlightSelector . '>
                <div class="blipper" data-dropdown=".blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown"></div>
                <div class="dropdown-menu dropdown-menu-arrow blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown gutter">
                    <h4>' . $title . '</h4>
                    <p>
                        ' . $html . '
                    </p>
                    <footer>
                        <button class="btn" data-action="walkthrough-previous">' . __('Previous', 'municipio-intranet') . '</button>
                        <button class="btn" data-action="walkthrough-next">' . __('Next', 'municipio-intranet') . '</button>
                        <button class="btn btn-plain" data-action="walkthrough-cancel">' . __('Cancel', 'municipio-intranet') . '</button>
                    </footer>
                </div>
            </div>
        ';
    }
}

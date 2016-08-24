<?php

$municipio_intranet_walkthrough_counter = 0;

if (!function_exists('municipio_intranet_walkthrough')) {
    /**
     * Formats a site's name correctly from a site array
     * @param  array $site The site to format name for
     * @return string
     */
    function municipio_intranet_walkthrough($title, $html, $highlightSelector = null)
    {
        if (!isset($_GET['walkthrough'])) {
            return;
        }

        global $municipio_intranet_walkthrough_counter;
        $municipio_intranet_walkthrough_counter++;

        if ($highlightSelector) {
            $highlightSelector = ' data-highlight="' . $highlightSelector . '"';
        }

        return '
            <div class="walkthrough" data-step="' . $municipio_intranet_walkthrough_counter . '"' . $highlightSelector . '>
                <div class="blipper" data-dropdown=".blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown"></div>
                <div class="dropdown-menu dropdown-menu-arrow blipper-' . $municipio_intranet_walkthrough_counter . '-dropdown gutter">
                    <h4>' . $title . '</h4>
                    <p>
                        ' . $html . '
                    </p>
                    <footer>
                        <button class="btn" data-action="walkthrough-previous">Previous</button>
                        <button class="btn" data-action="walkthrough-next">Next</button>
                        <button class="btn btn-plain" data-action="walkthrough-cancel">Cancel</button>
                    </footer>
                </div>
            </div>
        ';
    }
}

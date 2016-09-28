<?php

$municipio_intranet_walkthrough_counter = 0;

if (!function_exists('municipio_intranet_walkthrough')) {
    /**
     * Creates a walkthrough step
     * @param  string $title             The step headline/title
     * @param  string $html              HTML content
     * @param  string $highlightSelector Selector for element to highlight when step is active
     * @param  string $position          The position of the blipper
     * @param  string $dropdownPosition  The position of dropdown
     * @param  array  $css               CSS rules ($key => $value)
     * @return string                    Walkthrough markup
     */
    function municipio_intranet_walkthrough($title, $html, $highlightSelector = null, $position = 'center', $dropdownPosition = 'center', $css = array())
    {
        if (!isset($_GET['walkthrough'])) {
            return;
        }

        global $municipio_intranet_walkthrough_counter;
        $municipio_intranet_walkthrough_counter++;

        if ($highlightSelector) {
            $highlightSelector = ' data-highlight="' . $highlightSelector . '"';
        }

        $styleTag = null;
        if (is_array($css) && count($css) > 0) {
            $styleTag = ' style="';
            foreach ($css as $key => $value) {
                $styleTag .= $key . ':' . $value . ';';
            }
            $styleTag .= '"';
        }

        switch ($position) {
            default:
                $position = 'walkthrough-center';
                break;

            case 'left':
                $position = 'walkthrough-left';
                break;

            case 'right':
                $position = 'walkthrough-right';
                break;
        }

        switch ($dropdownPosition) {
            default:
                $dropdownPosition = 'walkthrough-dropdown-center';
                break;

            case 'left':
                $dropdownPosition = 'walkthrough-dropdown-left';
                break;

            case 'right':
                $dropdownPosition = 'walkthrough-dropdown-right';
                break;
        }

        return '
            <div class="walkthrough ' . $position . ' ' . $dropdownPosition . '" data-step="' . $municipio_intranet_walkthrough_counter . '"' . $highlightSelector . $styleTag . '>
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

if (!function_exists('municipio_intranet_field_example')) {
    function municipio_intranet_field_example($key, $example, $label = null)
    {
        if (is_null($label)) {
            $label = __('Example', 'municipio-intranet');
        }

        $example = apply_filters('MunicipioIntranet/EditProfile/Example/Example', array(
            'label' => $label,
            'example' => $example
        ), $key);

        echo '<small>' . $example['label'] . ': ' . $example['example'] . '</small>';
    }
}

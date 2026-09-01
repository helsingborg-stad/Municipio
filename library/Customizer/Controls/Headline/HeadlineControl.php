<?php

namespace Municipio\Customizer\Controls\Headline;

use WP_Customize_Control;

class HeadlineControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_headline';

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        if (!empty($this->label)) {
            echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
        }

        if (!empty($this->description)) {
            echo '<span class="description customize-control-description">' . esc_html($this->description) . '</span>';
        }
    }
}

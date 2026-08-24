<?php

namespace Municipio\Customizer\Controls\CustomContent;

use WP_Customize_Control;

class CustomContentControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_custom_content';

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        echo wp_kses_post((string) $this->value());
    }
}

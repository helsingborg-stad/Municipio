<?php

namespace Municipio\Customizer\Controls\Divider;

use WP_Customize_Control;

class DividerControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_divider';

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        echo '<hr />';
    }
}

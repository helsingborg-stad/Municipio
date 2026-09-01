<?php

namespace Municipio\Customizer\Controls\CheckboxSwitch;

use WP_Customize_Control;

/**
 * Render switch-like checkbox controls with title/description layout
 * consistent with other custom controls.
 */
class CheckboxSwitchControl extends WP_Customize_Control
{
    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_style(
            'municipio-customizer-checkbox-switch',
            get_template_directory_uri() . '/library/Customizer/Controls/CheckboxSwitch/CheckboxSwitchControl.css',
        );
    }

    /**
     * Render the control content.
     *
     * @return void
     */
    protected function render_content(): void
    {
        if (empty($this->label)) {
            return;
        }

        ?>
        <label class="municipio-control municipio-control--checkbox-switch">
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>

            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>

            <span class="municipio-control__checkbox-switch-input">
                <input type="checkbox" value="1" <?php $this->link(); checked($this->value()); ?> />
            </span>
        </label>
        <?php
    }
}

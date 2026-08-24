<?php

namespace Municipio\Customizer\Controls\Slider;

use WP_Customize_Control;

class SliderControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_slider';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_style(
            'municipio-customizer-slider',
            get_template_directory_uri() . '/library/Customizer/Controls/Slider/SliderControl.css',
        );

        wp_enqueue_script(
            'municipio-customizer-slider',
            get_template_directory_uri() . '/library/Customizer/Controls/Slider/SliderControl.js',
            ['customize-controls'],
            null,
            true,
        );
    }

    /**
     * Render control content.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $value = (string) $this->value();
        $min = $this->input_attrs['min'] ?? 0;
        $max = $this->input_attrs['max'] ?? 100;
        $step = $this->input_attrs['step'] ?? 1;
        ?>
        <label class="municipio-control municipio-control--slider">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>

            <input type="hidden" class="municipio-slider-value" value="<?php echo esc_attr($value); ?>" <?php $this->link(); ?> />
            <div class="municipio-slider-inputs">
                <input
                    type="range"
                    class="municipio-slider-range"
                    value="<?php echo esc_attr($value); ?>"
                    min="<?php echo esc_attr((string) $min); ?>"
                    max="<?php echo esc_attr((string) $max); ?>"
                    step="<?php echo esc_attr((string) $step); ?>"
                />
                <input
                    type="number"
                    class="municipio-slider-number"
                    value="<?php echo esc_attr($value); ?>"
                    min="<?php echo esc_attr((string) $min); ?>"
                    max="<?php echo esc_attr((string) $max); ?>"
                    step="<?php echo esc_attr((string) $step); ?>"
                />
            </div>
        </label>
        <?php
    }
}

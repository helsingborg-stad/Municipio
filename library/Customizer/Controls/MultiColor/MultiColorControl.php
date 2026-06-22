<?php

namespace Municipio\Customizer\Controls\MultiColor;

use WP_Customize_Control;

class MultiColorControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_multicolor';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script(
            'municipio-customizer-multicolor',
            get_template_directory_uri() . '/library/Customizer/Controls/MultiColor/MultiColorControl.js',
            ['customize-controls', 'wp-color-picker'],
            null,
            true,
        );

        wp_enqueue_style(
            'municipio-customizer-multicolor',
            get_template_directory_uri() . '/library/Customizer/Controls/MultiColor/MultiColorControl.css',
        );
    }

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $values = $this->getValues();
        ?>
        <div class="municipio-control municipio-control--multicolor">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-multicolor-value" value="<?php echo esc_attr(wp_json_encode($values)); ?>" <?php $this->link(); ?> />
            <?php foreach ($this->choices as $choiceValue => $choiceLabel): ?>
                <label class="municipio-multicolor-field">
                    <span><?php echo esc_html((string) $choiceLabel); ?></span>
                    <input type="text" class="municipio-multicolor-input" data-choice="<?php echo esc_attr((string) $choiceValue); ?>" value="<?php echo esc_attr((string) ($values[$choiceValue] ?? '')); ?>" />
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Get color values.
     *
     * @return array<string, string>
     */
    private function getValues(): array
    {
        $value = $this->value();

        if (is_string($value)) {
            $decodedValue = json_decode($value, true);
            $value = is_array($decodedValue) ? $decodedValue : [];
        }

        return is_array($value) ? $value : [];
    }
}

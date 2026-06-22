<?php

namespace Municipio\Customizer\Controls\MultiCheck;

use WP_Customize_Control;

class MultiCheckControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_multicheck';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_script(
            'municipio-customizer-multicheck',
            get_template_directory_uri() . '/library/Customizer/Controls/MultiCheck/MultiCheckControl.js',
            ['customize-controls'],
            null,
            true,
        );

        wp_enqueue_style(
            'municipio-customizer-multicheck',
            get_template_directory_uri() . '/library/Customizer/Controls/MultiCheck/MultiCheckControl.css',
        );
    }

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $selectedValues = $this->getSelectedValues();
        ?>
        <label class="municipio-control municipio-control--multicheck">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
        </label>
        <input type="hidden" class="municipio-multicheck-value" value="<?php echo esc_attr(wp_json_encode($selectedValues)); ?>" <?php $this->link(); ?> />
        <ul class="municipio-multicheck-options">
            <?php foreach ($this->choices as $choiceValue => $choiceLabel): ?>
                <li class="municipio-multicheck-options__item">
                    <label>
                        <input type="checkbox" value="<?php echo esc_attr((string) $choiceValue); ?>" <?php checked(in_array((string) $choiceValue, $selectedValues, true)); ?> />
                        <?php echo esc_html((string) $choiceLabel); ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }

    /**
     * Get selected values as strings.
     *
     * @return array<int, string>
     */
    private function getSelectedValues(): array
    {
        $value = $this->value();

        if (is_string($value)) {
            $decodedValue = json_decode($value, true);
            $value = is_array($decodedValue) ? $decodedValue : [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        return array_map(static fn($item): string => (string) $item, $value);
    }
}

<?php

namespace Municipio\Customizer\Controls\MultiCheck;

use Municipio\Customizer\Controls\CustomizerControlAssets;
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
        CustomizerControlAssets::enqueueScript();

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
        $layoutClassName = $this->getLayoutClassName();
        ?>
        <municipio-multicheck-control class="municipio-control municipio-control--multicheck">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-multicheck-value" value="<?php echo esc_attr(wp_json_encode($selectedValues)); ?>" <?php $this->link(); ?> />
            <ul class="municipio-multicheck-options <?php echo esc_attr($layoutClassName); ?>">
            <?php foreach ($this->choices as $choiceValue => $choiceLabel): ?>
                <li class="municipio-multicheck-options__item">
                    <label class="municipio-multicheck-options__label">
                        <input class="municipio-multicheck-options__input" type="checkbox" value="<?php echo esc_attr((string) $choiceValue); ?>" <?php checked(in_array((string) $choiceValue, $selectedValues, true)); ?> />
                        <?php echo esc_html((string) $choiceLabel); ?>
                    </label>
                </li>
            <?php endforeach; ?>
            </ul>
        </municipio-multicheck-control>
        <?php
    }

    /**
     * Get multicheck options layout class name.
     *
     * @return string
     */
    private function getLayoutClassName(): string
    {
        $layout = is_string($this->input_attrs['layout'] ?? null) ? trim($this->input_attrs['layout']) : '';

        return $layout === 'horizontal' ? 'municipio-multicheck-options--horizontal' : '';
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

<?php

namespace Municipio\Customizer\Controls\MultiSelect;

use Municipio\Customizer\Controls\CustomizerControlAssets;
use WP_Customize_Control;

class MultiSelectControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_multiselect';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        CustomizerControlAssets::enqueueScript();

        wp_enqueue_style(
            'municipio-customizer-multiselect',
            get_template_directory_uri() . '/library/Customizer/Controls/MultiSelect/MultiSelectControl.css',
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
        $choices = $this->getChoices();
        ?>
        <municipio-multiselect-control class="municipio-control municipio-control--multiselect" data-max-items="<?php echo esc_attr((string) $this->getMaxItems()); ?>">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-multiselect-value" value="<?php echo esc_attr(wp_json_encode($selectedValues)); ?>" <?php $this->link(); ?> />
            <div class="municipio-multiselect-picker">
                <select class="municipio-multiselect-picker__select">
                    <option value=""><?php esc_html_e('Select value', 'municipio'); ?></option>
                    <?php foreach ($choices as $choiceValue => $choiceLabel): ?>
                        <option value="<?php echo esc_attr($choiceValue); ?>" <?php disabled(in_array($choiceValue, $selectedValues, true)); ?>>
                            <?php echo esc_html($choiceLabel); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="button municipio-multiselect-picker__add"><?php esc_html_e('Add', 'municipio'); ?></button>
            </div>
            <ul class="municipio-multiselect-pills">
                <?php foreach ($selectedValues as $selectedValue): ?>
                    <?php if (!array_key_exists($selectedValue, $choices)) {
                        continue;
                    } ?>
                    <li class="municipio-multiselect-pill" data-multiselect-value="<?php echo esc_attr($selectedValue); ?>">
                        <span class="municipio-multiselect-pill__label"><?php echo esc_html($choices[$selectedValue]); ?></span>
                        <button type="button" class="municipio-multiselect-pill__remove" aria-label="<?php esc_attr_e('Remove', 'municipio'); ?>">&times;</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </municipio-multiselect-control>
        <?php
    }

    /**
     * Get normalized selected values.
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

        return array_values(array_unique(array_filter(
            array_map(static fn($item): string => (string) $item, $value),
            static fn(string $item): bool => $item !== '',
        )));
    }

    /**
     * Get normalized choices.
     *
     * @return array<string, string>
     */
    private function getChoices(): array
    {
        $choices = [];

        foreach ($this->choices as $choiceValue => $choiceLabel) {
            $choices[(string) $choiceValue] = (string) $choiceLabel;
        }

        return $choices;
    }

    /**
     * Get max selected item count.
     *
     * @return int
     */
    private function getMaxItems(): int
    {
        $multiple = $this->input_attrs['multiple'] ?? null;

        return is_numeric($multiple) ? max(0, (int) $multiple) : 0;
    }
}

<?php

namespace Municipio\Customizer\Controls\Sortable;

use WP_Customize_Control;

class SortableControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_sortable';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_script(
            'municipio-customizer-sortable',
            get_template_directory_uri() . '/library/Customizer/Controls/Sortable/SortableControl.js',
            ['customize-controls', 'jquery-ui-sortable'],
            null,
            true,
        );

        wp_enqueue_style(
            'municipio-customizer-sortable',
            get_template_directory_uri() . '/library/Customizer/Controls/Sortable/SortableControl.css',
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
        <div class="municipio-control municipio-control--sortable">
            <?php if ($this->label !== ''): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if ($this->description !== ''): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-sortable-value" value="<?php echo esc_attr(wp_json_encode($selectedValues)); ?>" <?php $this->link(); ?> />
            <ul class="municipio-sortable-items">
                <?php foreach ($this->getOrderedChoices($selectedValues) as $choiceValue => $choiceLabel): ?>
                    <li class="municipio-sortable-item" data-sortable-value="<?php echo esc_attr((string) $choiceValue); ?>">
                        <span class="municipio-sortable-item__handle" aria-hidden="true"></span>
                        <label>
                            <input type="checkbox" value="<?php echo esc_attr((string) $choiceValue); ?>" <?php checked(in_array((string) $choiceValue, $selectedValues, true)); ?> />
                            <?php echo esc_html((string) $choiceLabel); ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Get choices ordered by saved value first, then unsaved choices.
     *
     * @param array<int, string> $selectedValues Selected values.
     *
     * @return array<string, string>
     */
    private function getOrderedChoices(array $selectedValues): array
    {
        $orderedChoices = [];
        $choices = array_map(static fn($choiceLabel): string => (string) $choiceLabel, $this->choices);

        foreach ($selectedValues as $selectedValue) {
            if (array_key_exists($selectedValue, $choices)) {
                $orderedChoices[$selectedValue] = $choices[$selectedValue];
            }
        }

        foreach ($choices as $choiceValue => $choiceLabel) {
            if (!array_key_exists((string) $choiceValue, $orderedChoices)) {
                $orderedChoices[(string) $choiceValue] = $choiceLabel;
            }
        }

        return $orderedChoices;
    }

    /**
     * Get selected sortable values as strings.
     *
     * @return array<int, string>
     */
    private function getSelectedValues(): array
    {
        $value = $this->value();

        if (is_string($value)) {
            $decodedValue = json_decode($value, true);
            $value = is_array($decodedValue) ? $decodedValue : [];
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map(static fn($item): string => (string) $item, $value));
    }
}

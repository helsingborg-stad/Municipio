<?php

namespace Municipio\Customizer\Controls\Sortable;

use Municipio\Customizer\Controls\CustomizerControlAssets;
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
        CustomizerControlAssets::enqueueScript();

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
        $orderedChoices = $this->getOrderedChoices($selectedValues);
        $baseSettingId = $this->getBaseSettingId();
        ?>
        <municipio-sortable-control class="municipio-control municipio-control--sortable" data-sortable-setting="<?php echo esc_attr($this->id); ?>" data-sortable-base-setting="<?php echo esc_attr($baseSettingId); ?>" data-sortable-hidden-setting="header_sortable_hidden_storage">
            <?php if ($this->label !== ''): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if ($this->description !== ''): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-sortable-value" value="<?php echo esc_attr(wp_json_encode($selectedValues)); ?>" <?php $this->link(); ?> />
            <div class="municipio-sortable-picker">
                <select class="municipio-sortable-picker__select">
                    <option value=""><?php esc_html_e('Select value', 'municipio'); ?></option>
                    <?php foreach ($orderedChoices as $choiceValue => $choiceLabel): ?>
                        <option value="<?php echo esc_attr((string) $choiceValue); ?>" <?php disabled(in_array((string) $choiceValue, $selectedValues, true)); ?>>
                            <?php echo esc_html((string) $choiceLabel); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <ul class="municipio-sortable-items">
                <?php foreach ($orderedChoices as $choiceValue => $choiceLabel): ?>
                    <?php if (!in_array((string) $choiceValue, $selectedValues, true)) {
                        continue;
                    } ?>
                    <li class="municipio-sortable-item" data-sortable-value="<?php echo esc_attr((string) $choiceValue); ?>" data-sortable-label="<?php echo esc_attr((string) $choiceLabel); ?>">
                        <span class="municipio-sortable-item__handle" data-tooltip="<?php esc_attr_e('Drag to reorder', 'municipio'); ?>" aria-hidden="true"></span>
                        <span class="municipio-sortable-item__label"><?php echo esc_html((string) $choiceLabel); ?></span>
                        <div class="municipio-sortable-item__actions">
                            <button type="button" class="button button-small municipio-sortable-option municipio-sortable-option--align" data-sortable-option="align" data-sortable-values="left,center,right"><span class="dashicons municipio-sortable-action__icon municipio-sortable-option__icon" aria-hidden="true"></span></button>
                            <button type="button" class="button button-small municipio-sortable-option municipio-sortable-option--margin" data-sortable-option="margin" data-sortable-values="none,left,right,both"><span class="dashicons municipio-sortable-action__icon municipio-sortable-option__icon" aria-hidden="true"></span></button>
                            <button type="button" class="button button-small municipio-sortable-remove" data-tooltip="<?php esc_attr_e('Remove', 'municipio'); ?>" aria-label="<?php esc_attr_e('Remove', 'municipio'); ?>"><span class="dashicons dashicons-trash municipio-sortable-action__icon" aria-hidden="true"></span></button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </municipio-sortable-control>
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

    /**
     * Get the base setting id used by hidden flexible header item options.
     *
     * @return string
     */
    private function getBaseSettingId(): string
    {
        return str_ends_with($this->id, '_responsive') ? substr($this->id, 0, -11) : $this->id;
    }
}

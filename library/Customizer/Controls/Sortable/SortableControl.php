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
                        <div class="municipio-sortable-item__content">
                            <span class="municipio-sortable-item__handle" data-tooltip="<?php esc_attr_e('Drag to reorder', 'municipio'); ?>" aria-hidden="true"></span>
                            <span class="municipio-sortable-item__label"><?php echo esc_html((string) $choiceLabel); ?></span>
                            <div class="municipio-sortable-item__actions">
                                <button type="button" class="button button-small municipio-sortable-settings-toggle" data-tooltip="<?php esc_attr_e('Settings', 'municipio'); ?>" aria-label="<?php esc_attr_e('Settings', 'municipio'); ?>" aria-expanded="false"><span class="dashicons dashicons-admin-generic municipio-sortable-action__icon" aria-hidden="true"></span></button>
                                <button type="button" class="button button-small municipio-sortable-remove" data-tooltip="<?php esc_attr_e('Remove', 'municipio'); ?>" aria-label="<?php esc_attr_e('Remove', 'municipio'); ?>"><span class="dashicons dashicons-trash municipio-sortable-action__icon" aria-hidden="true"></span></button>
                            </div>
                        </div>
                        <div class="municipio-sortable-item__settings" hidden>
                            <?php $this->renderItemSettings((string) $choiceValue); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <label>
                <?php esc_html_e('Stored settings data (debug)', 'municipio'); ?>
                <textarea class="municipio-sortable-hidden-storage-debug" data-sortable-hidden-setting="header_sortable_hidden_storage" rows="6" readonly spellcheck="false"></textarea>
            </label>
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
        return $this->id;
    }

    /**
     * Render the alignment and margin inputs for one sortable item.
     *
     * @param string $choiceValue Item identifier.
     *
     * @return void
     */
    private function renderItemSettings(string $choiceValue): void
    {
        $itemId = sanitize_html_class($this->id . '-' . $choiceValue);
        $settings = [
            'align' => [
                'label' => __('Alignment', 'municipio'),
                'options' => [
                    'left' => __('Align left', 'municipio'),
                    'center' => __('Align center', 'municipio'),
                    'right' => __('Align right', 'municipio'),
                ],
            ],
            'margin' => [
                'label' => __('Margin', 'municipio'),
                'options' => [
                    'none' => __('No margin', 'municipio'),
                    'left' => __('Left margin', 'municipio'),
                    'right' => __('Right margin', 'municipio'),
                    'both' => __('Both margins', 'municipio'),
                ],
            ],
        ];
        ?>
        <?php foreach ($settings as $settingName => $setting): ?>
            <fieldset class="municipio-sortable-item__settings-group">
                <legend><?php echo esc_html($setting['label']); ?></legend>
                <div class="municipio-sortable-item__settings-options">
                    <?php foreach ($setting['options'] as $optionValue => $optionLabel): ?>
                        <?php $inputId = $itemId . '-' . $settingName . '-' . $optionValue; ?>
                        <label for="<?php echo esc_attr($inputId); ?>">
                            <input id="<?php echo esc_attr($inputId); ?>" class="municipio-sortable-setting-input" type="radio" name="<?php echo esc_attr($itemId . '-' . $settingName); ?>" value="<?php echo esc_attr($optionValue); ?>" data-sortable-option="<?php echo esc_attr($settingName); ?>" />
                            <?php echo esc_html($optionLabel); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
        <?php
    }
}

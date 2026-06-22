<?php

namespace Municipio\Customizer\Controls\Repeater;

use WP_Customize_Control;

class RepeaterControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_repeater';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_script(
            'municipio-customizer-repeater',
            get_template_directory_uri() . '/library/Customizer/Controls/Repeater/RepeaterControl.js',
            ['customize-controls'],
            null,
            true,
        );

        wp_enqueue_style(
            'municipio-customizer-repeater',
            get_template_directory_uri() . '/library/Customizer/Controls/Repeater/RepeaterControl.css',
        );
    }

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $rows = $this->getRows();
        $fields = $this->getFields();
        ?>
        <div class="municipio-control municipio-control--repeater" data-fields="<?php echo esc_attr(wp_json_encode($fields)); ?>">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-repeater-value" value="<?php echo esc_attr(wp_json_encode($rows)); ?>" <?php $this->link(); ?> />
            <div class="municipio-repeater-rows">
                <?php foreach ($rows as $row): ?>
                    <?php $this->renderRow($fields, is_array($row) ? $row : []); ?>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button municipio-repeater-add"><?php esc_html_e('Add row', 'municipio'); ?></button>
        </div>
        <?php
    }

    /**
     * Render a repeater row.
     *
     * @param array $fields Row fields.
     * @param array $row    Row values.
     *
     * @return void
     */
    private function renderRow(array $fields, array $row): void
    { ?>
        <div class="municipio-repeater-row">
            <?php foreach ($fields as $fieldKey => $field): ?>
                <label class="municipio-repeater-field">
                    <span><?php echo esc_html((string) ($field['label'] ?? $fieldKey)); ?></span>
                    <input type="<?php echo esc_attr((string) ($field['type'] ?? 'text')); ?>" data-repeater-key="<?php echo esc_attr((string) $fieldKey); ?>" value="<?php echo esc_attr((string) ($row[$fieldKey] ?? $field['default'] ?? '')); ?>" />
                </label>
            <?php endforeach; ?>
            <button type="button" class="button-link-delete municipio-repeater-remove"><?php esc_html_e('Remove', 'municipio'); ?></button>
        </div>
        <?php }

    /**
     * Get row field definitions.
     *
     * @return array
     */
    private function getFields(): array
    {
        return is_array($this->input_attrs['fields'] ?? null) ? $this->input_attrs['fields'] : [];
    }

    /**
     * Get repeater rows.
     *
     * @return array
     */
    private function getRows(): array
    {
        $value = $this->value();

        if (is_string($value)) {
            $decodedValue = json_decode($value, true);
            $value = is_array($decodedValue) ? $decodedValue : [];
        }

        return is_array($value) ? $value : [];
    }
}

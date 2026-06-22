<?php

namespace Municipio\Customizer\Controls\Background;

use WP_Customize_Control;

class BackgroundControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_background';

    private const BACKGROUND_FIELDS = [
        'background-color' => 'text',
        'background-image' => 'url',
        'background-repeat' => 'select',
        'background-position' => 'text',
        'background-size' => 'select',
        'background-attachment' => 'select',
    ];

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_script(
            'municipio-customizer-background',
            get_template_directory_uri() . '/library/Customizer/Controls/Background/BackgroundControl.js',
            ['customize-controls'],
            null,
            true,
        );

        wp_enqueue_style(
            'municipio-customizer-background',
            get_template_directory_uri() . '/library/Customizer/Controls/Background/BackgroundControl.css',
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
        <div class="municipio-control municipio-control--background">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-background-value" value="<?php echo esc_attr(wp_json_encode($values)); ?>" <?php $this->link(); ?> />
            <?php foreach (self::BACKGROUND_FIELDS as $fieldKey => $fieldType): ?>
                <label class="municipio-background-field">
                    <span><?php echo esc_html($this->getFieldLabel($fieldKey)); ?></span>
                    <?php $this->renderField($fieldKey, $fieldType, (string) ($values[$fieldKey] ?? '')); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render a background sub field.
     *
     * @param string $fieldKey   Field key.
     * @param string $fieldType  Field type.
     * @param string $fieldValue Current field value.
     *
     * @return void
     */
    private function renderField(string $fieldKey, string $fieldType, string $fieldValue): void
    {
        if ($fieldType !== 'select') {
            printf(
                '<input type="%s" data-background-key="%s" value="%s" />',
                esc_attr($fieldType),
                esc_attr($fieldKey),
                esc_attr($fieldValue),
            );
            return;
        }

        echo '<select data-background-key="' . esc_attr($fieldKey) . '">';

        foreach ($this->getSelectOptions($fieldKey) as $optionValue => $optionLabel) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($optionValue),
                selected($fieldValue, $optionValue, false),
                esc_html($optionLabel),
            );
        }

        echo '</select>';
    }

    /**
     * Get select options for a background field.
     *
     * @param string $fieldKey Field key.
     *
     * @return array<string, string>
     */
    private function getSelectOptions(string $fieldKey): array
    {
        return match ($fieldKey) {
            'background-repeat' => [
                'repeat' => __('Repeat', 'municipio'),
                'no-repeat' => __('No repeat', 'municipio'),
                'repeat-x' => __('Repeat horizontal', 'municipio'),
                'repeat-y' => __('Repeat vertical', 'municipio'),
            ],
            'background-size' => [
                'auto' => __('Auto', 'municipio'),
                'cover' => __('Cover', 'municipio'),
                'contain' => __('Contain', 'municipio'),
            ],
            'background-attachment' => [
                'scroll' => __('Scroll', 'municipio'),
                'fixed' => __('Fixed', 'municipio'),
            ],
            default => [],
        };
    }

    /**
     * Get a readable field label.
     *
     * @param string $fieldKey Field key.
     *
     * @return string
     */
    private function getFieldLabel(string $fieldKey): string
    {
        return ucwords(str_replace(['background-', '-'], ['', ' '], $fieldKey));
    }

    /**
     * Get background values.
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

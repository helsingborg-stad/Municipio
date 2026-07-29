<?php

namespace Municipio\Customizer\Controls\ColorChoice;

use Municipio\Customizer\Controls\CustomizerControlAssets;
use Municipio\Helper\ColorSwatches;
use WP_Customize_Control;

class ColorChoiceControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_color_choice';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        CustomizerControlAssets::enqueueScript();

        wp_enqueue_style(
            'municipio-customizer-color-choice',
            get_template_directory_uri() . '/library/Customizer/Controls/ColorChoice/ColorChoiceControl.css',
        );
    }

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $currentValue = (string) $this->value();
        $choiceSwatches = $this->getChoiceSwatches();
        ?>
        <municipio-color-choice-control class="municipio-control municipio-control--color-choice">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-color-choice-value" value="<?php echo esc_attr($currentValue); ?>" <?php $this->link(); ?> />
            <div class="municipio-color-choice-options" role="group" aria-label="<?php echo esc_attr($this->label ?: __('Color choice', 'municipio')); ?>">
                <?php foreach ($choiceSwatches as $choice): ?>
                    <?php $isActive = strtolower($choice['value']) === strtolower($currentValue); ?>
                    <button
                        type="button"
                        class="municipio-color-choice-swatch<?php echo $isActive ? ' is-active' : ''; ?>"
                        data-value="<?php echo esc_attr($choice['value']); ?>"
                        aria-label="<?php echo esc_attr($choice['label']); ?>"
                        title="<?php echo esc_attr($choice['label']); ?>"
                    >
                        <span
                            class="municipio-color-choice-swatch__sample"
                            style="background-color: <?php echo esc_attr($choice['background']); ?>; color: <?php echo esc_attr($choice['contrast']); ?>;"
                            aria-hidden="true"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" focusable="false" aria-hidden="true">
                                <path d="M280-160v-520H80v-120h520v120H400v520H280Zm360 0v-320H520v-120h360v120H760v320H640Z"/>
                            </svg>
                        </span>
                        <span class="screen-reader-text"><?php echo esc_html($choice['label']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </municipio-color-choice-control>
        <?php
    }

    /**
     * Build swatch metadata for available choices.
     *
     * @return array<int, array{value:string,label:string,background:string,contrast:string}>
     */
    private function getChoiceSwatches(): array
    {
        $tokenValues = ColorSwatches::getTokenPaletteValues();
        $choiceSwatches = [];

        foreach ($this->choices as $choiceValue => $choiceLabel) {
            $choiceValue = (string) $choiceValue;

            $background = $this->getChoiceBackgroundColor($choiceValue, $tokenValues);
            $contrast = $this->getChoiceContrastColor($choiceValue, $tokenValues);

            $choiceSwatches[] = [
                'value' => $choiceValue,
                'label' => (string) $choiceLabel,
                'background' => $background,
                'contrast' => $contrast,
            ];
        }

        return $choiceSwatches;
    }

    /**
     * Resolve a background swatch color for a choice.
     *
     * @param string               $choiceValue
     * @param array<string, mixed> $tokenValues
     *
     * @return string
     */
    private function getChoiceBackgroundColor(string $choiceValue, array $tokenValues): string
    {
        return match ($choiceValue) {
            'primary' => (string) ($tokenValues['--color--primary'] ?? 'var(--color--primary, #005fa3)'),
            'secondary' => (string) ($tokenValues['--color--secondary'] ?? 'var(--color--secondary, #1d2327)'),
            default => '#dcdcde',
        };
    }

    /**
     * Resolve a contrast swatch color for a choice.
     *
     * @param string               $choiceValue
     * @param array<string, mixed> $tokenValues
     *
     * @return string
     */
    private function getChoiceContrastColor(string $choiceValue, array $tokenValues): string
    {
        return match ($choiceValue) {
            'primary' => (string) ($tokenValues['--color--primary-contrast'] ?? '#ffffff'),
            'secondary' => (string) ($tokenValues['--color--secondary-contrast'] ?? '#ffffff'),
            default => '#1d2327',
        };
    }
}

<?php

namespace Municipio\Customizer\Controls\MultiColor;

use Municipio\Customizer\Controls\CustomizerControlAssets;
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
        CustomizerControlAssets::enqueueScript();

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
        $palettes = $this->getPalettes();
        $palettePairs = $this->getPalettePairs($palettes);
        ?>
        <municipio-multicolor-control
            class="municipio-control municipio-control--multicolor"
            data-palette-pairs="<?php echo esc_attr(wp_json_encode($palettePairs)); ?>"
        >
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
                    <input type="hidden" class="municipio-multicolor-input" data-choice="<?php echo esc_attr((string) $choiceValue); ?>" value="<?php echo esc_attr((string) ($values[$choiceValue] ?? '')); ?>" />
                    <div class="municipio-multicolor-swatches" role="group" aria-label="<?php echo esc_attr((string) $choiceLabel); ?>">
                        <?php foreach ($palettePairs as $pair): ?>
                            <?php $isActive = strtolower((string) ($values[$choiceValue] ?? '')) === strtolower($pair['background']); ?>
                            <button
                                type="button"
                                class="municipio-multicolor-swatch<?php echo $isActive ? ' is-active' : ''; ?>"
                                data-choice="<?php echo esc_attr((string) $choiceValue); ?>"
                                data-background="<?php echo esc_attr($pair['background']); ?>"
                                data-contrast="<?php echo esc_attr($pair['contrast']); ?>"
                                aria-label="<?php echo esc_attr(sprintf('%s / %s', $pair['background'], $pair['contrast'])); ?>"
                                title="<?php echo esc_attr(sprintf('%s / %s', $pair['background'], $pair['contrast'])); ?>"
                            >
                                <span
                                    class="municipio-multicolor-swatch__sample"
                                    style="background-color: <?php echo esc_attr($pair['background']); ?>; color: <?php echo esc_attr($pair['contrast']); ?>;"
                                    aria-hidden="true"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" focusable="false" aria-hidden="true">
                                        <path d="M280-160v-520H80v-120h520v120H400v520H280Zm360 0v-320H520v-120h360v120H760v320H640Z"/>
                                    </svg>
                                </span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </label>
            <?php endforeach; ?>
        </municipio-multicolor-control>
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

    /**
     * Get sanitized palettes from control input attributes.
     *
     * @return array<int, string>
     */
    private function getPalettes(): array
    {
        $palettes = $this->input_attrs['palettes'] ?? [];

        if (!is_array($palettes)) {
            return [];
        }

        return array_values(array_filter(
            array_map(static function (mixed $value): string {
                return is_string($value) ? trim($value) : '';
            }, $palettes),
            static function (string $value): bool {
                return $value !== '';
            },
        ));
    }

    /**
     * Build background/contrast swatch pairs from a flat palette list.
     *
     * @param array<int, string> $palettes
     *
     * @return array<int, array{background:string, contrast:string}>
     */
    private function getPalettePairs(array $palettes): array
    {
        $pairs = [];

        for ($index = 0; $index < count($palettes); $index += 2) {
            $background = $palettes[$index] ?? null;
            $contrast = $palettes[$index + 1] ?? null;

            if (!is_string($background) || trim($background) === '') {
                continue;
            }

            if (!is_string($contrast) || trim($contrast) === '') {
                $contrast = '#ffffff';
            }

            $pairs[] = [
                'background' => strtolower(trim($background)),
                'contrast' => strtolower(trim($contrast)),
            ];
        }

        return array_values(array_unique($pairs, SORT_REGULAR));
    }
}

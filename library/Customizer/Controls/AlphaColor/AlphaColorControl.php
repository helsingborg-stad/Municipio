<?php

namespace Municipio\Customizer\Controls\AlphaColor;

use WP_Customize_Control;

class AlphaColorControl extends WP_Customize_Control
{
    /**
     * Prevent duplicate inline event binding script output.
     *
     * @var bool
     */
    private static bool $inlineScriptPrinted = false;

    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_alpha_color';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_style(
            'municipio-customizer-alpha-color',
            get_template_directory_uri() . '/library/Customizer/Controls/AlphaColor/AlphaColorControl.css',
        );
    }

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $palettePairs = $this->getPalettePairs();
        $swatchPairRole = $this->getSwatchPairRole();
        $pairedSetting = $this->getPairedSetting();
        $currentValue = (string) $this->value();
        ?>
        <label
            class="municipio-control municipio-control--alpha-color"
            data-swatch-role="<?php echo esc_attr($swatchPairRole); ?>"
            data-paired-setting="<?php echo esc_attr($pairedSetting); ?>"
        >
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="hidden" class="municipio-alpha-color-value" value="<?php echo esc_attr($currentValue); ?>" <?php $this->link(); ?> />
            <?php if (!empty($palettePairs)): ?>
                <div class="municipio-alpha-color-palettes" aria-label="<?php echo esc_attr__('Color swatches', 'municipio'); ?>">
                    <?php foreach ($palettePairs as $pair): ?>
                        <?php

                        $buttonValue = $swatchPairRole === 'contrast' ? $pair['contrast'] : $pair['background'];
                        $isActive = strtolower($buttonValue) === strtolower($currentValue);
                        ?>
                        <button
                            type="button"
                            class="municipio-alpha-color-swatch<?php echo $isActive ? ' is-active' : ''; ?>"
                            data-background="<?php echo esc_attr($pair['background']); ?>"
                            data-contrast="<?php echo esc_attr($pair['contrast']); ?>"
                            aria-label="<?php echo esc_attr(sprintf('%s / %s', $pair['background'], $pair['contrast'])); ?>"
                            title="<?php echo esc_attr(sprintf('%s / %s', $pair['background'], $pair['contrast'])); ?>"
                        >
                            <span
                                class="municipio-alpha-color-swatch__sample"
                                aria-hidden="true"
                                style="background-color: <?php echo esc_attr($pair['background']); ?>; color: <?php echo esc_attr($pair['contrast']); ?>;"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" focusable="false" aria-hidden="true">
                                    <path d="M280-160v-520H80v-120h520v120H400v520H280Zm360 0v-320H520v-120h360v120H760v320H640Z"/>
                                </svg>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </label>
        <?php

        if (!self::$inlineScriptPrinted) {
            self::$inlineScriptPrinted = true;
            echo
                '<script>(function(){document.addEventListener("click",function(event){var target=event.target;if(!(target instanceof Element)){return;}var button=target.closest(".municipio-alpha-color-swatch");if(!(button instanceof HTMLElement)){return;}var control=button.closest(".municipio-control--alpha-color");if(!(control instanceof HTMLElement)){return;}var input=control.querySelector(".municipio-alpha-color-value");if(!(input instanceof HTMLInputElement)){return;}var background=button.getAttribute("data-background")||"";var contrast=button.getAttribute("data-contrast")||"";if(!background||!contrast){return;}var role=control.getAttribute("data-swatch-role")||"background";var nextValue=role==="contrast"?contrast:background;input.value=nextValue;control.querySelectorAll(".municipio-alpha-color-swatch").forEach(function(node){node.classList.remove("is-active");});button.classList.add("is-active");input.dispatchEvent(new Event("input",{bubbles:true}));input.dispatchEvent(new Event("change",{bubbles:true}));var pairedSetting=control.getAttribute("data-paired-setting")||"";if(pairedSetting&&window.wp&&typeof window.wp.customize==="function"){var pairedValue=role==="contrast"?background:contrast;var setting=window.wp.customize(pairedSetting);if(setting&&typeof setting.set==="function"){setting.set(pairedValue);}}});})();</script>'
            ;
        }
    }

    /**
     * Get sanitized swatch pair role.
     *
     * @return string
     */
    private function getSwatchPairRole(): string
    {
        $role = $this->input_attrs['swatch_pair_role'] ?? 'background';

        return $role === 'contrast' ? 'contrast' : 'background';
    }

    /**
     * Get paired setting key.
     *
     * @return string
     */
    private function getPairedSetting(): string
    {
        $setting = $this->input_attrs['paired_setting'] ?? '';

        return is_string($setting) ? trim($setting) : '';
    }

    /**
     * Get sanitized background/contrast pairs from control input attributes.
     *
     * @return array<int, array{background:string, contrast:string}>
     */
    private function getPalettePairs(): array
    {
        $palettePairs = $this->input_attrs['palette_pairs'] ?? [];

        if (!is_array($palettePairs)) {
            return [];
        }

        $sanitizedPairs = [];
        foreach ($palettePairs as $pair) {
            if (!is_array($pair)) {
                continue;
            }

            $background = is_string($pair['background'] ?? null) ? trim($pair['background']) : '';
            $contrast = is_string($pair['contrast'] ?? null) ? trim($pair['contrast']) : '';

            if ($background === '' || $contrast === '') {
                continue;
            }

            $sanitizedPairs[] = [
                'background' => $background,
                'contrast' => $contrast,
            ];
        }

        return array_values(array_unique($sanitizedPairs, SORT_REGULAR));
    }
}

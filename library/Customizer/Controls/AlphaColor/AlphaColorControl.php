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
        $palettes = $this->getPalettes();
        ?>
        <label class="municipio-control municipio-control--alpha-color">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="text" value="<?php echo esc_attr((string) $this->value()); ?>" placeholder="rgba(255, 255, 255, 1)" <?php $this->link(); ?> />
            <?php if (!empty($palettes)): ?>
                <div class="municipio-alpha-color-palettes" aria-label="<?php echo esc_attr__('Color swatches', 'municipio'); ?>">
                    <?php foreach ($palettes as $palette): ?>
                        <button
                            type="button"
                            class="municipio-alpha-color-swatch"
                            data-color="<?php echo esc_attr($palette); ?>"
                            style="background-color: <?php echo esc_attr($palette); ?>; width: 1.2rem; height: 1.2rem; border: 1px solid #dcdcde; border-radius: 999px; margin-right: .4rem; margin-top: .4rem;"
                            title="<?php echo esc_attr($palette); ?>"
                            aria-label="<?php echo esc_attr($palette); ?>"
                        ></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </label>
        <?php

        if (!self::$inlineScriptPrinted) {
            self::$inlineScriptPrinted = true;
            echo '<script>(function(){document.addEventListener("click",function(event){var target=event.target;if(!(target instanceof HTMLElement)||!target.classList.contains("municipio-alpha-color-swatch")){return;}var control=target.closest(".municipio-control--alpha-color");if(!control){return;}var input=control.querySelector("input[type=text]");if(!(input instanceof HTMLInputElement)){return;}var color=target.getAttribute("data-color");if(!color){return;}input.value=color;input.dispatchEvent(new Event("input",{bubbles:true}));input.dispatchEvent(new Event("change",{bubbles:true}));});})();</script>';
        }
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

        return array_values(array_filter(array_map(static function (mixed $value): string {
            return is_string($value) ? trim($value) : '';
        }, $palettes), static function (string $value): bool {
            return $value !== '';
        }));
    }
}

<?php

namespace Municipio\Customizer\Controls\AlphaColor;

use WP_Customize_Control;

class AlphaColorControl extends WP_Customize_Control
{
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
    { ?>
        <label class="municipio-control municipio-control--alpha-color">
            <?php if (!empty($this->label)): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if (!empty($this->description)): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <input type="text" value="<?php echo esc_attr((string) $this->value()); ?>" placeholder="rgba(255, 255, 255, 1)" <?php $this->link(); ?> />
        </label>
        <?php }
}

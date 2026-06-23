<?php

namespace Municipio\Customizer\Controls\TabBox;

use Municipio\Customizer\Controls\CustomizerControlAssets;
use WP_Customize_Control;

class TabBoxControl extends WP_Customize_Control
{
    /**
     * Custom control type.
     *
     * @var string
     */
    public $type = 'municipio_tab_box';

    /**
     * Enqueue control assets.
     *
     * @return void
     */
    public function enqueue(): void
    {
        CustomizerControlAssets::enqueueScript();

        wp_enqueue_style(
            'municipio-customizer-tab-box',
            get_template_directory_uri() . '/library/Customizer/Controls/TabBox/TabBoxControl.css',
        );
    }

    /**
     * Render the control.
     *
     * @return void
     */
    protected function render_content(): void
    {
        $tabs = $this->getTabs();

        if (empty($tabs)) {
            return;
        }
        ?>
        <municipio-tab-box-control class="municipio-control municipio-control--tab-box" data-tab-box-id="<?php echo esc_attr($this->id); ?>">
            <?php if ($this->label !== ''): ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php if ($this->description !== ''): ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            <div class="municipio-tab-box" role="tablist">
                <?php foreach ($tabs as $tabId => $tab): ?>
                    <button
                        type="button"
                        class="municipio-tab-box__tab"
                        data-tab-box-tab="<?php echo esc_attr($tabId); ?>"
                        data-tab-box-controls="<?php echo esc_attr(wp_json_encode($tab['controls'])); ?>"
                        role="tab"
                        aria-selected="false"
                    >
                        <?php echo esc_html($tab['label']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </municipio-tab-box-control>
        <?php
    }

    /**
     * Get normalized tabs.
     *
     * @return array<string, array{label: string, controls: array<int, string>}>
     */
    private function getTabs(): array
    {
        $tabs = $this->input_attrs['tabs'] ?? $this->choices;

        if (!is_array($tabs)) {
            return [];
        }

        $normalizedTabs = [];

        foreach ($tabs as $tabId => $tab) {
            if (!is_array($tab)) {
                continue;
            }

            $controls = array_values(array_filter(
                array_map(static fn($control): string => (string) $control, $tab['controls'] ?? []),
                static fn(string $control): bool => $control !== '',
            ));

            if (empty($controls)) {
                continue;
            }

            $normalizedTabs[(string) $tabId] = [
                'label' => (string) ($tab['label'] ?? $tabId),
                'controls' => $controls,
            ];
        }

        return $normalizedTabs;
    }
}

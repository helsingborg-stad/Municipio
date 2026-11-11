<?php

declare(strict_types=1);

namespace Municipio\Admin\Acf;

use Municipio\Helper\ColorSwatches;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/**
 * Renders opt-in ACF colour pickers as semantic Styleguide palette roles.
 *
 * Add `municipio_palette_color => true` to an ACF colour picker. The field
 * definition remains a standard colour picker; its loaded runtime definition
 * is filtered into the restricted palette selector before ACF prepares the
 * editor. The saved value is a role such as `primary` or `palette-1`, never a
 * hex value.
 */
class PaletteColorChoices implements Hookable
{
    public function __construct(
        private AddFilter&AddAction $wpService,
        private EnqueueManagerInterface $enqueue,
    ) {}

    public function addHooks(): void
    {
        // Convert before ACF applies type-specific defaults and serialization.
        $this->wpService->addFilter('acf/validate_field', [$this, 'populateChoices'], 5);
        $this->wpService->addFilter('acf/load_field', [$this, 'populateChoices']);
        $this->wpService->addAction('acf/render_field_settings/type=color_picker', [$this, 'renderFieldSetting']);
        $this->wpService->addAction('acf/input/admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    /**
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function populateChoices(array $field): array
    {
        if (!in_array($field['municipio_palette_color'] ?? false, [true, 1, '1'], true)
            || (function_exists('acf_is_screen') && acf_is_screen('acf-field-group'))
            || (($_POST['action'] ?? '') === 'acf/field_group/render_field_settings')) {
            return $field;
        }

        // Keep the local JSON/PHP definition as an ACF colour picker. This
        // conversion affects only ACF's loaded runtime field definition.
        $field['type'] = 'select';
        $field['choices'] = ColorSwatches::getColorPairChoices();
        $field['return_format'] = 'value';
        $field['multiple'] = 0;
        $field['allow_custom'] = 0;
        $field['ui'] = 0;
        $field['ajax'] = 0;
        $field['create_options'] = 0;
        $field['save_options'] = 0;
        $field['allow_null'] ??= 1;
        $field['placeholder'] = $field['allow_null'] ? __('None', 'municipio') : $field['placeholder'] ?? '';
        $wrapperClasses = preg_split('/\s+/', trim($field['wrapper']['class'] ?? ''), flags: PREG_SPLIT_NO_EMPTY);
        $field['wrapper']['class'] = implode(' ', array_unique([...$wrapperClasses, 'municipio-palette-color-selector']));

        return $field;
    }

    /** Register a persistent ACF field-editor setting, not a content field. */
    public function renderFieldSetting(array $field): void
    {
        if (!function_exists('acf_render_field_setting')) {
            return;
        }

        acf_render_field_setting($field, [
            'label' => __('Use color palette', 'municipio'),
            'instructions' => __('Restrict this field to background and contrast pairs from the styleguide.', 'municipio'),
            'name' => 'municipio_palette_color',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 0,
        ]);
    }

    /**
     * Supplies the active Styleguide palette to the visual selector script.
     */
    public function enqueueAssets(): void
    {
        $choices = [];
        foreach (ColorSwatches::getColorPairChoices() as $role => $label) {
            $choices[] = ['value' => $role, 'label' => $label, ...ColorSwatches::getColorPair($role)];
        }

        $this->enqueue
            ->add('js/palette-color-choices.js', ['acf-input'])
            ->with()
            ->data('MunicipioPaletteColorChoices', [
                'choices' => $choices,
            ]);
    }
}

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
        $this->wpService->addAction('acf/input/admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    /**
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function populateChoices(array $field): array
    {
        if (($field['municipio_palette_color'] ?? false) !== true) {
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
        $field['placeholder'] = $field['allow_null'] ? __('None', 'municipio') : ($field['placeholder'] ?? '');
        $wrapperClasses = preg_split('/\s+/', trim($field['wrapper']['class'] ?? ''), flags: PREG_SPLIT_NO_EMPTY);
        $field['wrapper']['class'] = implode(' ', array_unique([...$wrapperClasses, 'municipio-palette-color-selector']));

        return $field;
    }

    /**
     * Supplies the active Styleguide palette to the visual selector script.
     */
    public function enqueueAssets(): void
    {
        $this->enqueue
            ->add('js/palette-color-choices.js', ['acf-input'])
            ->with()
            ->data('MunicipioPaletteColorChoices', [
                'choices' => ColorSwatches::getColorPairPreviews(),
            ]);
    }
}

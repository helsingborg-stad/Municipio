<?php

namespace Municipio\ThemeJson;

use WpService\WpService;

class CssVariableBridge
{
    /**
     * Mapping from existing CSS variable names to theme.json preset slugs.
     * This bridges --color-* variables to --wp--preset--color--* variables.
     */
    private array $variableMapping = [
        // Primary colors
        '--color-primary'             => 'primary',
        '--color-primary-dark'        => 'primary-dark',
        '--color-primary-light'       => 'primary-light',
        '--color-primary-contrasting' => 'primary-contrasting',

        // Secondary colors
        '--color-secondary'             => 'secondary',
        '--color-secondary-dark'        => 'secondary-dark',
        '--color-secondary-light'       => 'secondary-light',
        '--color-secondary-contrasting' => 'secondary-contrasting',

        // Background & text
        '--color-background' => 'background',
        '--color-base'       => 'foreground',

        // State colors
        '--color-success'             => 'success',
        '--color-success-contrasting' => 'success-contrasting',
        '--color-warning'             => 'warning',
        '--color-warning-contrasting' => 'warning-contrasting',
        '--color-danger'              => 'danger',
        '--color-danger-contrasting'  => 'danger-contrasting',
        '--color-info'                => 'info',
        '--color-info-contrasting'    => 'info-contrasting',
    ];

    /**
     * Default values for CSS variables (fallbacks if theme.json not loaded).
     * These match the defaults in Customizer/Sections/Colors.php.
     */
    private array $defaultValues = [
        '--color-primary'             => '#ae0b05',
        '--color-primary-dark'        => '#770000',
        '--color-primary-light'       => '#e84c31',
        '--color-primary-contrasting' => '#ffffff',
        '--color-secondary'             => '#ec6701',
        '--color-secondary-dark'        => '#b23700',
        '--color-secondary-light'       => '#ff983e',
        '--color-secondary-contrasting' => '#ffffff',
        '--color-background'            => '#f5f5f5',
        '--color-base'                  => '#000000',
        '--color-success'               => '#91d736',
        '--color-success-contrasting'   => '#000000',
        '--color-warning'               => '#efbb21',
        '--color-warning-contrasting'   => '#000000',
        '--color-danger'                => '#d73740',
        '--color-danger-contrasting'    => '#ffffff',
        '--color-info'                  => '#3d3d3d',
        '--color-info-contrasting'      => '#ffffff',
    ];

    public function __construct(private WpService $wpService)
    {
        // Output bridge CSS early to ensure it's available before component styles
        $this->wpService->addAction('wp_head', [$this, 'outputCssVariableBridge'], 5);
        $this->wpService->addAction('admin_head', [$this, 'outputCssVariableBridge'], 5);
    }

    /**
     * Output CSS that bridges WordPress preset variables to existing CSS variable names.
     * This maintains backwards compatibility with existing component styles.
     */
    public function outputCssVariableBridge(): void
    {
        $css = $this->generateBridgeCss();

        if (empty($css)) {
            return;
        }

        echo '<style id="theme-json-css-bridge">' . "\n";
        echo $css;
        echo '</style>' . "\n";
    }

    /**
     * Generate the bridge CSS content.
     *
     * @return string CSS content
     */
    private function generateBridgeCss(): string
    {
        $rules = [];

        foreach ($this->variableMapping as $existingVar => $presetSlug) {
            $fallback = $this->defaultValues[$existingVar] ?? 'inherit';
            $rules[] = sprintf(
                '    %s: var(--wp--preset--color--%s, %s);',
                $existingVar,
                $presetSlug,
                $fallback
            );
        }

        if (empty($rules)) {
            return '';
        }

        return ":root {\n" . implode("\n", $rules) . "\n}\n";
    }
}

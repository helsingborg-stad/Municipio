<?php

declare(strict_types=1);

namespace Municipio\Customizer;

/**
 * Registers a consistent set of Customizer controls for button variants.
 */
final class ButtonSettings
{
    /**
     * @param array{
     *     sectionID: string,
     *     settingPrefix: string,
     *     settingSuffixes: array<string, string>,
     *     labels: array<string, string>,
     *     outputs: array<string, array<int, array<string, mixed>>>,
     *     activeCallback?: array<int, array<string, mixed>>,
     *     defaults?: array<string, string>,
     *     priority?: int,
     * } $config
     */
    public function __construct(array $config)
    {
        $this->sectionID = $config['sectionID'];
        $this->settingPrefix = $config['settingPrefix'];
        $this->settingSuffixes = $config['settingSuffixes'];
        $this->labels = $config['labels'];
        $this->outputs = $config['outputs'];
        $this->activeCallback = $config['activeCallback'] ?? [];
        $this->defaults = $config['defaults'] ?? [];
        $this->priority = $config['priority'] ?? 10;

        foreach (['style', 'size', 'color'] as $setting) {
            CustomizerField::addField($this->makeField($setting));
        }
    }

    private string $sectionID;
    private string $settingPrefix;
    /** @var array<string, string> */
    private array $settingSuffixes;
    /** @var array<string, string> */
    private array $labels;
    /** @var array<string, array<int, array<string, mixed>>> */
    private array $outputs;
    /** @var array<int, array<string, mixed>> */
    private array $activeCallback;
    /** @var array<string, string> */
    private array $defaults;
    private int $priority;

    /** @return array<string, mixed> */
    private function makeField(string $setting): array
    {
        $field = [
            'type' => 'select',
            'settings' => $this->settingPrefix . '_' . ($this->settingSuffixes[$setting] ?? $setting),
            'label' => $this->labels[$setting] ?? esc_html__('Button ' . $setting, 'municipio'),
            'section' => $this->sectionID,
            'default' => $this->defaults[$setting] ?? $this->getDefault($setting),
            'priority' => $this->priority,
            'choices' => $this->getChoices($setting),
            'output' => $this->outputs[$setting] ?? [['type' => 'controller']],
        ];

        if ($this->activeCallback !== []) {
            $field['active_callback'] = $this->activeCallback;
        }

        return $field;
    }

    private function getDefault(string $setting): string
    {
        return match ($setting) {
            'style' => 'filled',
            'size' => 'md',
            'color' => 'inherit',
        };
    }

    /** @return array<string, string> */
    private function getChoices(string $setting): array
    {
        return match ($setting) {
            'style' => [
                'filled' => esc_html__('Filled', 'municipio'),
                'basic' => esc_html__('Basic', 'municipio'),
                'outlined' => esc_html__('Outlined', 'municipio'),
            ],
            'size' => [
                'sm' => esc_html__('Small', 'municipio'),
                'md' => esc_html__('Medium', 'municipio'),
                'lg' => esc_html__('Large', 'municipio'),
            ],
            'color' => [
                'inherit' => esc_html__('Inherit', 'municipio'),
                'primary' => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
        };
    }
}

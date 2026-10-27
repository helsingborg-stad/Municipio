<?php

declare(strict_types=1);

namespace Municipio\SiteHealth;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Reports whether the required PHP Tidy extension is available.
 */
final class TidyExtensionRequirement implements Hookable
{
    /** @var Closure(): bool */
    private readonly Closure $isTidyExtensionLoaded;

    /**
     * @param null|callable(): bool $isTidyExtensionLoaded
     */
    public function __construct(
        private readonly AddFilter $wpService,
        ?callable $isTidyExtensionLoaded = null,
    ) {
        $this->isTidyExtensionLoaded = $isTidyExtensionLoaded instanceof Closure
            ? $isTidyExtensionLoaded
            : Closure::fromCallable($isTidyExtensionLoaded ?? static fn(): bool => extension_loaded('tidy'));
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('site_status_tests', [$this, 'addTest']);
    }

    /**
     * Adds Municipio's required PHP extension check to Site Health.
     *
     * @param array<string, mixed> $tests
     * @return array<string, mixed>
     */
    public function addTest(array $tests): array
    {
        $tests['direct']['municipio_tidy_extension'] = [
            'label' => __('Check the PHP Tidy extension', 'municipio'),
            'test'  => [$this, 'testTidyExtension'],
        ];

        return $tests;
    }

    /**
     * @return array{label: string, status: 'good'|'critical', badge: array{label: string, color: string}, description: string, actions: string, test: string}
     */
    public function testTidyExtension(): array
    {
        if (($this->isTidyExtensionLoaded)()) {
            return [
                'label'       => __('The PHP Tidy extension is available.', 'municipio'),
                'status'      => 'good',
                'badge'       => [
                    'label' => __('Municipio', 'municipio'),
                    'color' => 'blue',
                ],
                'description' => '<p>' . __('The PHP Tidy extension is installed and enabled.', 'municipio') . '</p>',
                'actions'     => '',
                'test'        => 'municipio_tidy_extension',
            ];
        }

        return [
            'label'       => __('The PHP Tidy extension is required.', 'municipio'),
            'status'      => 'critical',
            'badge'       => [
                'label' => __('Municipio', 'municipio'),
                'color' => 'blue',
            ],
            'description' => '<p>' . __('Install and enable the PHP Tidy extension on the server.', 'municipio') . '</p>',
            'actions'     => '',
            'test'        => 'municipio_tidy_extension',
        ];
    }
}

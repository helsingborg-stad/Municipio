<?php

declare(strict_types=1);

namespace Municipio\Integrations\Sentry;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\IsAdmin;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/**
 * Enqueues privacy-conscious browser performance monitoring when configured.
 */
class SentryFeature implements Hookable
{
    public const CONFIG_FILTER = 'Municipio/Integrations/Sentry/Config';

    private string $ingestOrigin = '';

    public function __construct(
        private AddAction&AddFilter&ApplyFilters&IsAdmin $wpService,
        private EnqueueManagerInterface $enqueue,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'enqueueAssets']);

        $config = $this->getConfig();
        $this->ingestOrigin = $this->getIngestOrigin($config['dsn']);

        if ($config['enabled'] && $this->ingestOrigin !== '') {
            $this->wpService->addFilter('WpSecurity/Csp', [$this, 'addSentryCspDomain']);
        }
    }

    /**
     * Load the browser SDK only for public requests with an explicit configuration.
     */
    public function enqueueAssets(): void
    {
        if ($this->wpService->isAdmin()) {
            return;
        }

        $config = $this->getConfig();

        if (!$config['enabled'] || $config['dsn'] === '') {
            return;
        }

        $this->enqueue
            ->add('js/sentry-web-vitals.js')
            ->with()
            ->data('municipioSentryWebVitalsConfig', $config);
    }

    /**
     * Gets the public browser configuration. Secrets must never be included here.
     *
     * @return array{enabled: bool, dsn: string, environment: string, release: string, sampleRate: float, tracesSampleRate: float, interactionTargets: array<string, string>}
     */
    private function getConfig(): array
    {
        $config = $this->wpService->applyFilters(self::CONFIG_FILTER, [
            'enabled'            => false,
            'dsn'                => '',
            'environment'        => '',
            'release'            => '',
            'sampleRate'         => 0.1,
            'tracesSampleRate'   => 0.1,
            'interactionTargets' => [],
        ]);

        if (!is_array($config)) {
            $config = [];
        }

        return [
            'enabled'            => (bool) ($config['enabled'] ?? false),
            'dsn'                => is_string($config['dsn'] ?? null) ? $config['dsn'] : '',
            'environment'        => is_string($config['environment'] ?? null) ? $config['environment'] : '',
            'release'            => is_string($config['release'] ?? null) ? $config['release'] : '',
            'sampleRate'         => $this->normalizeRate($config['sampleRate'] ?? 0.1),
            'tracesSampleRate'   => $this->normalizeRate($config['tracesSampleRate'] ?? 0.1),
            'interactionTargets' => $this->normalizeInteractionTargets($config['interactionTargets'] ?? []),
        ];
    }

    private function normalizeRate(mixed $value): float
    {
        if (!is_numeric($value)) {
            return 0.1;
        }

        return max(0.0, min(1.0, (float) $value));
    }

    /**
     * @return array<string, string>
     */
    private function normalizeInteractionTargets(mixed $targets): array
    {
        if (!is_array($targets)) {
            return [];
        }

        $normalized = [];

        foreach ($targets as $selector => $name) {
            if (!is_string($selector) || !is_string($name) || $selector === '') {
                continue;
            }

            // Labels are telemetry dimensions, not user-facing text or DOM content.
            $label = preg_replace('/[^a-z0-9_-]/', '', strtolower($name));

            if ($label !== '') {
                $normalized[$selector] = substr($label, 0, 64);
            }
        }

        return $normalized;
    }

    /**
     * Adds only the configured Sentry ingest origin to the browser connection policy.
     */
    public function addSentryCspDomain(array $directives): array
    {
        $directives['connect-src'] ??= [];
        $directives['connect-src'][] = $this->ingestOrigin;

        return $directives;
    }

    private function getIngestOrigin(string $dsn): string
    {
        $parts = parse_url($dsn);

        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return '';
        }

        return sprintf('%s://%s', $parts['scheme'], $parts['host']);
    }
}

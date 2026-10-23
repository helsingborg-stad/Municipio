# Sentry Web Vitals

This opt-in integration sends CLS, FCP, INP, LCP and TTFB as Sentry distribution metrics.
It sends no raw DOM selector, element text, query parameter, form value, click breadcrumb or
personally identifiable information.
The Sentry SDK is dynamically imported after `load` and an idle period, and only for sampled page
views, so its 112 kB gzip browser bundle does not compete with the page's initial interaction.

Enable it from a site-specific plugin or `functions.php`:

```php
add_filter('Municipio/Integrations/Sentry/Config', static function (array $config): array {
    return [
        ...$config,
        'enabled' => true,
        'dsn' => 'https://public@example.ingest.sentry.io/123',
        'environment' => 'production',
        'release' => 'municipio-7.69.4',
        'sampleRate' => 0.1,
        'tracesSampleRate' => 0.1,
        'interactionTargets' => [
            '#mobile-menu-trigger-open' => 'header-menu',
            '#site-language-menu-button' => 'language-menu',
            '[data-open="search-modal"]' => 'header-search',
        ],
    ];
});
```

`sampleRate` controls the share of page views that initialize Sentry and send the custom
distribution metrics. Keep all Web Vitals for the sampled page views: filtering only slow values
would make percentile calculations invalid. Use stable, pre-approved labels in
`interactionTargets`; raw selectors and content are intentionally never sent.

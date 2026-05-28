<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Resolves Municipio home URLs to the active Polylang language home URL.
 */
class ResolveHomeUrl implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure  $languageHomeUrlResolver Optional language home URL resolver.
     */
    public function __construct(
        private AddFilter $wpService,
        private ?Closure $languageHomeUrlResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/homeUrl', [$this, 'resolveLanguageHomeUrl']);
    }

    /**
     * Resolve the Municipio home URL to the current Polylang language home URL.
     *
     * @param string $homeUrl The existing home URL.
     *
     * @return string The language-aware home URL when available, otherwise the original URL.
     */
    public function resolveLanguageHomeUrl(string $homeUrl): string
    {
        $languageHomeUrl = $this->getLanguageHomeUrlResolver()?->__invoke();

        if (!is_string($languageHomeUrl) || $languageHomeUrl === '') {
            return $homeUrl;
        }

        return rtrim($languageHomeUrl, '/');
    }

    /**
     * Get the language home URL resolver.
     *
     * @return ?Closure The language home URL resolver.
     */
    private function getLanguageHomeUrlResolver(): ?Closure
    {
        if ($this->languageHomeUrlResolver instanceof Closure) {
            return $this->languageHomeUrlResolver;
        }

        if (!is_callable('pll_home_url')) {
            return null;
        }

        return static fn (): mixed => call_user_func('pll_home_url');
    }
}

<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Resolves PDF not-found redirects to the current Polylang language base URL.
 */
class ResolvePdfNotFoundUrl implements Hookable
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
        $this->wpService->addFilter('Municipio/Pdf/NotFoundUrl', [$this, 'resolveNotFoundUrl']);
    }

    /**
     * Resolve the not-found URL for the current Polylang language.
     *
     * @param string $url The original not-found URL.
     *
     * @return string The resolved not-found URL.
     */
    public function resolveNotFoundUrl(string $url): string
    {
        $languageHomeUrl = $this->getLanguageHomeUrlResolver()?->__invoke();

        if (!is_string($languageHomeUrl) || $languageHomeUrl === '') {
            return $url;
        }

        return rtrim($languageHomeUrl, '/') . '/404';
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

<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Resolves navigation cache keys per Polylang language.
 */
class ResolveNavigationCacheKey implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure  $currentLanguageResolver Optional current language resolver.
     */
    public function __construct(
        private AddFilter $wpService,
        private ?Closure $currentLanguageResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Navigation/Cache/Key', [$this, 'appendCurrentLanguage']);
    }

    /**
     * Append the current Polylang language to the provided cache key.
     *
     * @param string $key The cache key.
     *
     * @return string The language-aware cache key.
     */
    public function appendCurrentLanguage(string $key): string
    {
        $currentLanguage = $this->getCurrentLanguageResolver()();

        if (!is_string($currentLanguage) || $currentLanguage === '') {
            return $key;
        }

        return sprintf('%s:%s', $key, $currentLanguage);
    }

    /**
     * Get the current language resolver.
     *
     * @return Closure The current language resolver.
     */
    private function getCurrentLanguageResolver(): Closure
    {
        if ($this->currentLanguageResolver instanceof Closure) {
            return $this->currentLanguageResolver;
        }

        return static function (): ?string {
            if (is_callable('pll_current_language')) {
                $language = call_user_func('pll_current_language', 'slug');

                if (is_string($language) && $language !== '') {
                    return $language;
                }
            }

            $requestLanguage = $_GET['lang'] ?? null;

            return is_string($requestLanguage) && $requestLanguage !== '' ? $requestLanguage : null;
        };
    }
}

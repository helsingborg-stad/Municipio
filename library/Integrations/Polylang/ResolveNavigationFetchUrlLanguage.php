<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Appends the current Polylang language to async navigation fetch URLs.
 */
class ResolveNavigationFetchUrlLanguage implements Hookable
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
        $this->wpService->addFilter('Municipio/Navigation/PageTree/FetchUrl', [$this, 'appendCurrentLanguage'], 10, 4);
    }

    /**
     * Append the current language to a page tree async fetch URL.
     *
     * @param string $fetchUrl The generated fetch URL.
     *
     * @return string The language-aware fetch URL.
     */
    public function appendCurrentLanguage(string $fetchUrl): string
    {
        $currentLanguage = $this->getCurrentLanguageResolver()();

        if (!is_string($currentLanguage) || $currentLanguage === '') {
            return $fetchUrl;
        }

        $parts = parse_url($fetchUrl);

        if (!is_array($parts)) {
            return $fetchUrl;
        }

        $query = [];
        parse_str($parts['query'] ?? '', $query);

        if (!empty($query['lang'])) {
            return $fetchUrl;
        }

        $query['lang'] = $currentLanguage;

        return $this->buildUrl($parts, $query);
    }

    /**
     * Build a URL from parse_url parts and query parameters.
     *
     * @param array<string, mixed> $parts URL parts.
     * @param array<string, mixed> $query Query parameters.
     *
     * @return string
     */
    private function buildUrl(array $parts, array $query): string
    {
        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host   = $parts['host'] ?? '';
        $port   = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user   = $parts['user'] ?? '';
        $pass   = isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $auth   = $user !== '' ? $user . $pass . '@' : '';
        $path   = $parts['path'] ?? '';
        $queryString = http_build_query($query);
        $fragment    = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return $scheme . $auth . $host . $port . $path . ($queryString !== '' ? '?' . $queryString : '') . $fragment;
    }

    /**
     * Get the current language resolver.
     *
     * @return Closure
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
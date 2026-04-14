<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WP_Query;
use WpService\Contracts\AddAction;

/**
 * Makes font attachment queries language agnostic when Polylang is active.
 */
class ResolveFontAttachmentQueries implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddAction $wpService The WordPress service.
     * @param ?Closure  $polylangIsActiveResolver Optional Polylang availability resolver.
     */
    public function __construct(
        private AddAction $wpService,
        private ?Closure $polylangIsActiveResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('pre_get_posts', [$this, 'makeFontAttachmentQueryLanguageAgnostic'], 1);
    }

    /**
     * Make font attachment queries language agnostic.
     *
     * @param WP_Query $query The query to inspect.
     *
     * @return void
     */
    public function makeFontAttachmentQueryLanguageAgnostic(WP_Query $query): void
    {
        if (!$this->isPolylangActive() || !$this->isFontAttachmentQuery($query)) {
            return;
        }

        $query->set('lang', '');
        $query->set('suppress_filters', true);
    }

    /**
     * Determine whether the query targets font attachments.
     *
     * @param WP_Query $query The query to inspect.
     *
     * @return bool True when the query targets font attachments, otherwise false.
     */
    private function isFontAttachmentQuery(WP_Query $query): bool
    {
        if ($query->get('post_type') !== 'attachment') {
            return false;
        }

        $postMimeType = $query->get('post_mime_type');

        if (is_string($postMimeType)) {
            return str_contains($postMimeType, 'font');
        }

        if (!is_array($postMimeType)) {
            return false;
        }

        foreach ($postMimeType as $mimeType) {
            if (is_string($mimeType) && str_contains($mimeType, 'font')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether Polylang is active.
     *
     * @return bool True when Polylang is active, otherwise false.
     */
    private function isPolylangActive(): bool
    {
        return $this->getPolylangIsActiveResolver()?->__invoke() ?? false;
    }

    /**
     * Get the Polylang availability resolver.
     *
     * @return ?Closure The Polylang availability resolver.
     */
    private function getPolylangIsActiveResolver(): ?Closure
    {
        if ($this->polylangIsActiveResolver instanceof Closure) {
            return $this->polylangIsActiveResolver;
        }

        if (!is_callable('pll_current_language')) {
            return null;
        }

        return static fn (): bool => true;
    }
}

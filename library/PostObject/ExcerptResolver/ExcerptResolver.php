<?php

declare(strict_types=1);

namespace Municipio\PostObject\ExcerptResolver;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\StripShortcodes;
use WpService\Contracts\WpStripAllTags;

class ExcerptResolver implements ExcerptResolverInterface
{
    public function __construct(
        private StripShortcodes&WpStripAllTags $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function resolveExcerpt(PostObjectInterface $postObject): string
    {
        if (strlen(trim($postObject->getExcerpt())) > 0) {
            return $this->prepareExcerpt($postObject->getExcerpt());
        }

        if (strlen(trim($postObject->getContent())) > 0) {
            return $this->prepareExcerpt($postObject->getContent());
        }

        return '';
    }

    private function prepareExcerpt(string $excerpt): string
    {
        if (strpos($excerpt, '<!--more-->') !== false) {
            $divided = explode('<!--more-->', $excerpt);
            $excerpt = $divided[0] !== '' ? $divided[0] : $excerpt;
        }

        $excerpt = $this->wpService->stripShortcodes($excerpt);
        $excerpt = $this->wpService->wpStripAllTags($excerpt, false);
        $excerpt = nl2br($excerpt);

        return trim($excerpt);
    }
}

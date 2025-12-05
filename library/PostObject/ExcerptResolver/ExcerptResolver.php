<?php

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
        if (!empty($postObject->getExcerpt())) {
            return $this->prepareExcerpt($postObject->getExcerpt());
        }

        if (!empty($postObject->getContent())) {
            return $this->prepareExcerpt($postObject->getContent());
        }

        return '';
    }

    private function prepareExcerpt(string $excerpt): string
    {
        if (strpos($excerpt, '<!--more-->')) {
            $divided = explode('<!--more-->', $excerpt);
            $excerpt = !empty($divided[0]) ? $divided[0] : $excerpt;
        }

        $excerpt = $this->wpService->stripShortcodes($excerpt);
        $excerpt = $this->wpService->wpStripAllTags($excerpt, false);
        $excerpt = nl2br($excerpt);

        return trim($excerpt);
    }
}

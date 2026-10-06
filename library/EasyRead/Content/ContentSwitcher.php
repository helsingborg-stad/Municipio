<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

use Municipio\EasyRead\Config\EasyReadConfigInterface;
use Municipio\EasyRead\Request\ReadableRequestInterface;
use WP_Post_Type;
use WpService\WpService;

/**
 * Delivers an alternative version of a post when ?readable=1 is requested.
 */
/**
 * @mago-expect lint:cyclomatic-complexity
 * @mago-expect lint:too-many-methods
 * This is the dedicated WordPress hook adapter for one feature.
 */
final class ContentSwitcher
{
    public function __construct(
        private WpService $wpService,
        private AlternativeContentRepositoryInterface $contentRepository,
        private EasyReadConfigInterface $config,
        private ReadableRequestInterface $request,
        private CurrentUrlInterface $currentUrl,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Accessibility/Items', [$this, 'addAccessibilityLink'], 11);
        $this->wpService->addFilter('Municipio/PostObject/getContent', [$this, 'replacePostObjectContent'], 10, 2);
        $this->wpService->addFilter('the_post', [$this, 'replacePostContent'], 9);
        $this->wpService->addFilter('the_lead', [$this, 'replaceLead']);
        // Run before WordPress' do_blocks callback (priority 9), so an
        // alternative saved with Gutenberg blocks is rendered normally.
        $this->wpService->addFilter('the_content', [$this, 'replaceContent'], 8);
    }

    public function addAccessibilityLink(mixed $items): array
    {
        $items = is_array($items) ? $items : [];
        $postId = $this->getTargetPostId();

        if ($postId === 0 || !$this->contentRepository->hasAlternative($postId)) {
            return $items;
        }

        $currentUrl = $this->currentUrl->get();
        $items[] = $this->request->isReadable()
            ? [
                'href' => $this->wpService->removeQueryArg($this->config->readableQueryParameter(), $currentUrl),
                'text' => $this->wpService->__('Default version', 'municipio'),
            ]
            : [
                'href' => $this->wpService->addQueryArg($this->config->readableQueryParameter(), '1', $currentUrl),
                'text' => $this->wpService->__('Easy to read', 'municipio'),
            ];

        return $items;
    }

    public function replacePostObjectContent(string $content, mixed $postObject): string
    {
        if (!$this->request->isReadable() || (!$this->wpService->isSingular() && !$this->wpService->isPostTypeArchive())) {
            return $content;
        }

        $postId = $this->getTargetPostId();
        $objectId = is_object($postObject) && property_exists($postObject, 'ID') ? (int) $postObject->ID : $postId;

        if ($objectId !== $postId && !$this->wpService->isPostTypeArchive()) {
            return $content;
        }

        return $this->alternativeOrOriginal($content, $postId);
    }

    public function replacePostContent(mixed $post): mixed
    {
        if (!$post instanceof \WP_Post || $post->ID !== $this->getTargetPostId() || !$this->shouldDisplay()) {
            return $post;
        }

        $post->post_content = $this->contentRepository->getAlternative($this->getTargetPostId());
        return $post;
    }

    public function replaceLead(string $lead): string
    {
        return $this->shouldDisplay() ? '' : $lead;
    }

    public function replaceContent(string $content): string
    {
        if (!$this->shouldDisplay()) {
            return $content;
        }

        $alternative = $this->contentRepository->getAlternative($this->getTargetPostId());

        // The global post remains the queried page while Modularity renders
        // sidebars and other modules. Only transform the page's own
        // alternative; leave every independently rendered module untouched.
        if ($content !== $alternative) {
            return $content;
        }

        // Gutenberg owns the rendering of its More block. Converting the raw
        // marker here would break the block comments before do_blocks runs.
        if ($this->wpService->hasBlocks($alternative)) {
            return $alternative;
        }

        return $this->formatAlternative($alternative);
    }

    private function shouldDisplay(): bool
    {
        $postId = $this->getTargetPostId();
        return $this->request->isReadable()
            && $postId !== 0
            && $this->isCurrentPostTarget($postId)
            && $this->contentRepository->hasAlternative($postId);
    }

    private function alternativeOrOriginal(string $original, int $postId): string
    {
        if ($postId === 0 || !$this->contentRepository->hasAlternative($postId)) {
            return $original;
        }

        $alternative = $this->contentRepository->getAlternative($postId);
        return $alternative === '' ? $original : $alternative;
    }

    private function getTargetPostId(): int
    {
        if ($this->wpService->isPostTypeArchive()) {
            $postType = $this->wpService->getQueriedObject();
            if ($postType instanceof WP_Post_Type) {
                return (int) $this->wpService->getOption('page_for_' . $postType->name);
            }
        }

        return $this->wpService->getQueriedObjectId();
    }

    private function isCurrentPostTarget(int $postId): bool
    {
        return $this->wpService->isPostTypeArchive() || (int) $this->wpService->getTheID() === $postId;
    }

    private function formatAlternative(string $content): string
    {
        if (!str_contains($content, '<!--more-->')) {
            return $content;
        }

        [$lead, $remainingContent] = explode('<!--more-->', $content, 2);
        return '<p class="lead">' . htmlspecialchars(strip_tags($lead), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>' . $remainingContent;
    }
}

<?php

declare(strict_types=1);

namespace Municipio\Integrations\Component;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Adds a frontend warning to images without alternative text for editors.
 */
class ImageAltTextIndicator implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'ComponentLibrary/Component/Image/Data',
            [$this, 'addIndicator'],
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function addIndicator(array $data): array
    {
        if (!$this->shouldShowIndicator() || !$this->isAltTextMissing($data['alt'] ?? null)) {
            return $data;
        }

        $attributes = $data['attributeList'] ?? [];
        $data['attributeList'] = is_array($attributes) ? $attributes : [];
        $data['attributeList']['data-a11y-error'] = $this->wpService->__('Alt text is missing', 'municipio');

        return $data;
    }

    private function shouldShowIndicator(): bool
    {
        return $this->wpService->isUserLoggedIn()
            && $this->wpService->currentUserCan('upload_files');
    }

    private function isAltTextMissing(mixed $altText): bool
    {
        return !is_string($altText) || trim($altText) === '';
    }
}

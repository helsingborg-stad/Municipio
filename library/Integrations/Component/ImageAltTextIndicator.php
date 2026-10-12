<?php

declare(strict_types=1);

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageInterface;
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
        if (!$this->shouldShowIndicator() || !$this->isAltTextMissing($this->getEffectiveAltText($data))) {
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

    /**
     * The component resolves a missing explicit alt text from an image contract
     * during rendering. Resolve it here as well so the warning reflects the
     * rendered image rather than its initial input data.
     *
     * @param array<string, mixed> $data
     */
    private function getEffectiveAltText(array $data): mixed
    {
        $altText = $data['alt'] ?? null;
        $source = $data['src'] ?? null;

        if ($this->isAltTextMissing($altText) && $source instanceof ImageInterface) {
            return $source->getAltText();
        }

        return $altText;
    }
}

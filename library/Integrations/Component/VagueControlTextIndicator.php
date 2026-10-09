<?php

declare(strict_types=1);

namespace Municipio\Integrations\Component;

use Municipio\HooksRegistrar\Hookable;
use Stringable;
use WpService\WpService;

/**
 * Adds a frontend warning to links and buttons with vague labels for editors.
 */
class VagueControlTextIndicator implements Hookable
{
    /** @var string[] */
    private const VAGUE_LABELS = [
        'click here',
        'här',
        'klicka här',
        'läs mer',
        'läs vidare',
        'more',
        'mer',
        'read more',
    ];

    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'ComponentLibrary/Component/Button/Data',
            [$this, 'addButtonIndicator'],
        );
        $this->wpService->addFilter(
            'ComponentLibrary/Component/Link/Data',
            [$this, 'addLinkIndicator'],
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function addButtonIndicator(array $data): array
    {
        return $this->addIndicator($data, $data['text'] ?? null, 'Button text is not descriptive enough');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function addLinkIndicator(array $data): array
    {
        $href = $data['href'] ?? null;

        if (!is_string($href) || trim($href) === '') {
            return $data;
        }

        return $this->addIndicator($data, $data['slot'] ?? null, 'Link text is not descriptive enough');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function addIndicator(array $data, mixed $label, string $message): array
    {
        if (!$this->shouldShowIndicator() || !$this->isVagueLabel($label)) {
            return $data;
        }

        $attributes = $data['attributeList'] ?? [];
        $data['attributeList'] = is_array($attributes) ? $attributes : [];
        $data['attributeList']['data-a11y-error'] = $this->wpService->__($message, 'municipio');

        return $data;
    }

    private function shouldShowIndicator(): bool
    {
        return $this->wpService->isUserLoggedIn()
            && $this->wpService->currentUserCan('upload_files');
    }

    private function isVagueLabel(mixed $label): bool
    {
        if (!is_string($label) && !($label instanceof Stringable)) {
            return false;
        }

        $label = html_entity_decode(strip_tags((string) $label), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $label = preg_replace('/\s+/u', ' ', trim($label));

        return is_string($label) && in_array(mb_strtolower($label, 'UTF-8'), self::VAGUE_LABELS, true);
    }
}

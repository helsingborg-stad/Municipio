<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\GetHiddenData;
use Municipio\Controller\Header\Helper\NormalizeOrderedItems;

/**
 * Resolves logotype scroll shrink data for flexible headers.
 */
class LogoScrollShrinkResolver
{
    private string $logoScrollShrinkSetting = 'headerLogoScrollShrink';
    private string $logoOverlapMultiplierSetting = 'headerLogoOverlapMultiplier';
    private string $logoScrollShrinkAspectRatioSetting = 'headerLogoScrollAspectRatio';

    /**
     * Constructor.
     */
    public function __construct(
        private object $customizer,
        private GetHiddenData $getHiddenData,
        private NormalizeOrderedItems $normalizeOrderedItems,
        private bool $isCustomizePreview = false,
    ) {
    }

    /**
     * Resolve the logo scroll shrink payload.
     *
     * @return array{enabled: bool, overlapMultiplier: float, aspectRatio: ?float, style: ?string, attributeList: array<string, string>}
     */
    public function resolve(): array
    {
        $enabled = $this->isEnabled();
        $overlapMultiplier = $this->getOverlapMultiplier();
        $aspectRatio = $this->getAspectRatio();
        $style = $enabled
            ? '--municipio-header-logo-overlap-multiplier: ' . $overlapMultiplier . ';'
            : null;

        return [
            'enabled' => $enabled,
            'overlapMultiplier' => $overlapMultiplier,
            'aspectRatio' => $aspectRatio,
            'style' => $style
        ];
    }

    /**
     * Determine if the header logotype scroll shrink behavior should be enabled.
     */
    private function isEnabled(): bool
    {
        if (empty($this->customizer->{$this->logoScrollShrinkSetting})) {
            return false;
        }

        return $this->hasLowerRowLogotype() && $this->isLowerRowLogotypeAlignedLeft();
    }

    /**
     * Determine if the desktop lower header row contains the logotype.
     */
    private function hasLowerRowLogotype(): bool
    {
        $lowerItems = $this->normalizeOrderedItems->normalize($this->customizer->headerSortableSectionMainLower ?? []);

        return in_array('logotype', $lowerItems, true);
    }

    /**
     * Determine if the desktop lower-row logotype is aligned left.
     */
    private function isLowerRowLogotypeAlignedLeft(): bool
    {
        $hiddenStorage = $this->getHiddenData->get();

        return ($hiddenStorage->header_sortable_section_main_lower->logotype->align ?? null) === 'left';
    }

    /**
     * Resolve the validated overlap multiplier for the logotype scroll effect.
     */
    private function getOverlapMultiplier(): float
    {
        $overlapMultiplier = (float) ($this->customizer->{$this->logoOverlapMultiplierSetting} ?? 0.25);

        return $overlapMultiplier >= 0 && $overlapMultiplier <= 1 ? $overlapMultiplier : 0.25;
    }

    /**
     * Resolve the validated aspect ratio for the logotype scroll effect.
     */
    private function getAspectRatio(): ?float
    {
        if ($this->isCustomizePreview) {
            return null;
        }

        $aspectRatio = (float) ($this->customizer->{$this->logoScrollShrinkAspectRatioSetting} ?? 1);

        return $aspectRatio > 0 ? $aspectRatio : null;
    }
}

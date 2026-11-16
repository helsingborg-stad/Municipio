<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Header;

/**
 * Resolves settings for the logotype scroll shrink behavior.
 */
class LogoScrollShrinkSettings
{
    private const SHRINK_SETTING = 'headerLogoScrollShrink';
    private const OVERLAP_MULTIPLIER_SETTING = 'headerLogoOverlapMultiplier';
    private const ASPECT_RATIO_SETTING = 'headerLogoScrollAspectRatio';

    /**
     * Constructor.
     */
    public function __construct(private object $customizer)
    {
    }

    /**
     * Resolve settings required by the view layer.
     *
     * @param Header|null $lowerHeader The lower header instance.
     * @param bool $isCustomizePreview Whether rendering in customize preview.
     * @return array{enabled: bool, overlapMultiplier: float, aspectRatio: ?float}
     */
    public function resolve(?Header $lowerHeader, bool $isCustomizePreview = false): array
    {
        $enabled = $this->isShrinkToggleEnabled() && $this->lowerHeaderHasLeftLogotype($lowerHeader);

        $overlapMultiplier = $this->resolveOverlapMultiplier();
        $aspectRatio = $this->resolveAspectRatio($isCustomizePreview);

        return [
            'enabled' => $enabled,
            'overlapMultiplier' => $overlapMultiplier,
            'aspectRatio' => $aspectRatio,
        ];
    }

    /**
     * Determine if the customizer shrink toggle is active.
     */
    private function isShrinkToggleEnabled(): bool
    {
        return !empty($this->customizer->{self::SHRINK_SETTING});
    }

    /**
     * Determine if lower header has a left-aligned logotype item.
     */
    private function lowerHeaderHasLeftLogotype(?Header $lowerHeader): bool
    {
        if (!$lowerHeader instanceof Header) {
            return false;
        }

        $leftItems = $lowerHeader->getMenuItemsForAlignment('left');

        return isset($leftItems['logotype']);
    }

    /**
     * Resolve overlap multiplier in the allowed range.
     */
    private function resolveOverlapMultiplier(): float
    {
        $overlapMultiplier = (float) ($this->customizer->{self::OVERLAP_MULTIPLIER_SETTING} ?? 0.25);

        if ($overlapMultiplier < 0 || $overlapMultiplier > 1) {
            return 0.25;
        }

        return $overlapMultiplier;
    }

    /**
     * Resolve optional aspect ratio for runtime rendering.
     */
    private function resolveAspectRatio(bool $isCustomizePreview): ?float
    {
        if ($isCustomizePreview) {
            return null;
        }

        $aspectRatioRaw = $this->customizer->{self::ASPECT_RATIO_SETTING} ?? 1;

        $aspectRatio = is_numeric($aspectRatioRaw) ? (float) $aspectRatioRaw : null;

        if ($aspectRatio !== null && $aspectRatio <= 0) {
            $aspectRatio = null;
        }

        return $aspectRatio;
    }
}
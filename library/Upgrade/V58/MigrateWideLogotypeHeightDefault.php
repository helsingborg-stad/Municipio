<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V58;

use WpService\Contracts\AttachmentUrlToPostid;
use WpService\Contracts\GetAttachedFile;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\WpGetimagesize;

/**
 * Sets safer logotype size defaults for very wide logotype sites.
 *
 * Uses measured logotype aspect ratio as a breakpoint signal for very wide
 * logos. For ratios >= 7:1, this migration applies safer defaults when legacy
 * values are too large. If ratio cannot be measured (for example, missing
 * offloaded media files) and legacy is still too large, a safe default is
 * applied instead of preserving the oversized legacy value.
 */
class MigrateWideLogotypeHeightDefault
{
    private const TOKENS_SETTING = 'tokens';
     private const LOGOTYPE_SETTING = 'logotype';
     private const CUSTOM_LOGO_SETTING = 'custom_logo';
    private const LEGACY_HEADER_LOGOTYPE_HEIGHT_SETTING = 'header_logotype_height';
     private const ATTACHMENT_METADATA_KEY = '_wp_attachment_metadata';

    private const CURRENT_LOGOTYPE_MULTIPLIER_KEY = '--logotype-height-multiplier';
    private const LEGACY_LOGOTYPE_MULTIPLIER_KEY = '--c-header--logotype-height-multiplier';
    private const LOGOTYPE_HEIGHT_TOKEN_KEY = '--c-header--logotype-height';
    private const LOGOTYPE_HEIGHT_MIN_MULTIPLIER_KEY = '--c-header--logotype-height-min-multiplier';
    private const LOGOTYPE_HEIGHT_MAX_MULTIPLIER_KEY = '--c-header--logotype-height-max-multiplier';

     private const WIDE_LOGOTYPE_RATIO_THRESHOLD = 7.0;
     private const LEGACY_MIN_REASONABLE_HEIGHT = 4.0;
     private const LEGACY_MAX_REASONABLE_HEIGHT = 4.5;
     private const DEFAULT_WIDE_LOGOTYPE_HEIGHT = 4.5;
    private const DEFAULT_WIDE_LOGOTYPE_MIN_MULTIPLIER = 5.0;
    private const DEFAULT_WIDE_LOGOTYPE_MAX_MULTIPLIER = 5.5;
    private const LEGACY_TO_MULTIPLIER_DIVISOR = 6.0;

    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod&GetPostMeta&AttachmentUrlToPostid&GetAttachedFile&WpGetimagesize $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod&GetPostMeta&AttachmentUrlToPostid&GetAttachedFile&WpGetimagesize $wpService,
    ) {}

    /**
     * Run migration.
     */
    public function migrate(): void
    {
        $ratio = $this->resolveLogotypeAspectRatio();
        $legacyHeight = $this->toFloatOrNull($this->wpService->getThemeMod(self::LEGACY_HEADER_LOGOTYPE_HEIGHT_SETTING, null));

        if ($ratio !== null && $ratio < self::WIDE_LOGOTYPE_RATIO_THRESHOLD) {
            return;
        }

        if ($ratio === null && ($legacyHeight === null || $legacyHeight <= self::LEGACY_MAX_REASONABLE_HEIGHT)) {
            return;
        }

        $targetLegacyHeight = $this->resolveTargetLegacyHeight($legacyHeight);

        $tokens = $this->getStoredTokens();
        $headerTokens = &$this->getGeneralHeaderTokens($tokens);
        $hasChanges = false;

        if (array_key_exists(self::LEGACY_LOGOTYPE_MULTIPLIER_KEY, $headerTokens)) {
            $legacyTokenValue = $headerTokens[self::LEGACY_LOGOTYPE_MULTIPLIER_KEY];
            unset($headerTokens[self::LEGACY_LOGOTYPE_MULTIPLIER_KEY]);

            if (!array_key_exists(self::CURRENT_LOGOTYPE_MULTIPLIER_KEY, $headerTokens)) {
                $headerTokens[self::CURRENT_LOGOTYPE_MULTIPLIER_KEY] = $legacyTokenValue;
            }

            $hasChanges = true;
        }

        $currentMultiplier = $this->toFloatOrNull($headerTokens[self::CURRENT_LOGOTYPE_MULTIPLIER_KEY] ?? null);
        $targetMultiplier = $targetLegacyHeight / self::LEGACY_TO_MULTIPLIER_DIVISOR;
        if ($currentMultiplier === null || abs($currentMultiplier - $targetMultiplier) > 0.0001) {
            $headerTokens[self::CURRENT_LOGOTYPE_MULTIPLIER_KEY] = $targetMultiplier;
            $hasChanges = true;
        }

        $currentMinMultiplier = $this->toFloatOrNull($headerTokens[self::LOGOTYPE_HEIGHT_MIN_MULTIPLIER_KEY] ?? null);
        if ($currentMinMultiplier === null || abs($currentMinMultiplier - self::DEFAULT_WIDE_LOGOTYPE_MIN_MULTIPLIER) > 0.0001) {
            $headerTokens[self::LOGOTYPE_HEIGHT_MIN_MULTIPLIER_KEY] = self::DEFAULT_WIDE_LOGOTYPE_MIN_MULTIPLIER;
            $hasChanges = true;
        }

        $currentMaxMultiplier = $this->toFloatOrNull($headerTokens[self::LOGOTYPE_HEIGHT_MAX_MULTIPLIER_KEY] ?? null);
        if ($currentMaxMultiplier === null || abs($currentMaxMultiplier - self::DEFAULT_WIDE_LOGOTYPE_MAX_MULTIPLIER) > 0.0001) {
            $headerTokens[self::LOGOTYPE_HEIGHT_MAX_MULTIPLIER_KEY] = self::DEFAULT_WIDE_LOGOTYPE_MAX_MULTIPLIER;
            $hasChanges = true;
        }

        if (array_key_exists(self::LOGOTYPE_HEIGHT_TOKEN_KEY, $headerTokens)) {
            unset($headerTokens[self::LOGOTYPE_HEIGHT_TOKEN_KEY]);
            $hasChanges = true;
        }

        if (!$hasChanges) {
            return;
        }

        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
    }

    /**
     * @return array<string, mixed>
     */
    private function getStoredTokens(): array
    {
        $default = ['token' => [], 'component' => []];
        $raw = $this->wpService->getThemeMod(self::TOKENS_SETTING, null);

        if (is_array($raw)) {
            return $raw;
        }

        if (!is_string($raw) || trim($raw) === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $default;
    }

    /**
     * Returns the mutable general header token scope.
     *
     * @param array<string, mixed> $tokens
     * @return array<string, mixed>
     */
    private function &getGeneralHeaderTokens(array &$tokens): array
    {
        if (!isset($tokens['component']) || !is_array($tokens['component'])) {
            $tokens['component'] = [];
        }

        if (!isset($tokens['component']['__general__']) || !is_array($tokens['component']['__general__'])) {
            $tokens['component']['__general__'] = [];
        }

        if (!isset($tokens['component']['__general__']['header']) || !is_array($tokens['component']['__general__']['header'])) {
            $tokens['component']['__general__']['header'] = [];
        }

        return $tokens['component']['__general__']['header'];
    }

    private function resolveTargetLegacyHeight(?float $legacyHeight): float
    {
        if ($legacyHeight !== null
            && $legacyHeight >= self::LEGACY_MIN_REASONABLE_HEIGHT
            && $legacyHeight <= self::LEGACY_MAX_REASONABLE_HEIGHT
        ) {
            return $legacyHeight;
        }

        return self::DEFAULT_WIDE_LOGOTYPE_HEIGHT;
    }

    private function resolveLogotypeAspectRatio(): ?float
    {
        $customLogoId = (int) ($this->wpService->getThemeMod(self::CUSTOM_LOGO_SETTING, 0) ?: 0);
        if ($customLogoId > 0) {
            $ratio = $this->resolveAttachmentAspectRatio($customLogoId);
            if ($ratio !== null) {
                return $ratio;
            }
        }

        $logotypeUrl = $this->wpService->getThemeMod(self::LOGOTYPE_SETTING, null);
        if (!is_string($logotypeUrl) || trim($logotypeUrl) === '') {
            return null;
        }

        $attachmentId = $this->wpService->attachmentUrlToPostid($logotypeUrl);
        if ($attachmentId <= 0) {
            return null;
        }

        return $this->resolveAttachmentAspectRatio($attachmentId);
    }

    private function resolveAttachmentAspectRatio(int $attachmentId): ?float
    {
        $metadata = $this->wpService->getPostMeta($attachmentId, self::ATTACHMENT_METADATA_KEY, true);
        $decodedMetadata = $this->decodeMetadata($metadata);

        if (is_array($decodedMetadata)) {
            $ratio = $this->ratioFromDimensions($decodedMetadata['width'] ?? null, $decodedMetadata['height'] ?? null);
            if ($ratio !== null) {
                return $ratio;
            }
        }

        $filePath = $this->wpService->getAttachedFile($attachmentId, true);
        if (!is_string($filePath) || $filePath === '' || !is_file($filePath)) {
            return null;
        }

        if (strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'svg') {
            return $this->ratioFromSvgFile($filePath);
        }

        $imageInfo = [];
        $dimensions = $this->wpService->wpGetimagesize($filePath, $imageInfo);
        if (!is_array($dimensions)) {
            return null;
        }

        return $this->ratioFromDimensions($dimensions[0] ?? null, $dimensions[1] ?? null);
    }

    private function decodeMetadata(mixed $metadata): mixed
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (!is_string($metadata) || trim($metadata) === '') {
            return null;
        }

        $unserialized = @unserialize($metadata);

        if ($unserialized !== false || $metadata === 'b:0;') {
            return $unserialized;
        }

        $decoded = json_decode($metadata, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function ratioFromSvgFile(string $filePath): ?float
    {
        $svg = @file_get_contents($filePath);
        if (!is_string($svg) || trim($svg) === '') {
            return null;
        }

        if (preg_match('/\bviewBox\s*=\s*["\']\s*([0-9.\-]+)\s+([0-9.\-]+)\s+([0-9.\-]+)\s+([0-9.\-]+)\s*["\']/i', $svg, $matches) === 1) {
            return $this->ratioFromDimensions($matches[3] ?? null, $matches[4] ?? null);
        }

        if (
            preg_match('/\bwidth\s*=\s*["\']\s*([0-9.]+)(?:px)?\s*["\']/i', $svg, $widthMatches) === 1
            && preg_match('/\bheight\s*=\s*["\']\s*([0-9.]+)(?:px)?\s*["\']/i', $svg, $heightMatches) === 1
        ) {
            return $this->ratioFromDimensions($widthMatches[1] ?? null, $heightMatches[1] ?? null);
        }

        return null;
    }

    private function ratioFromDimensions(mixed $width, mixed $height): ?float
    {
        $widthValue = $this->toFloatOrNull($width);
        $heightValue = $this->toFloatOrNull($height);

        if ($widthValue === null || $heightValue === null || $heightValue <= 0.0) {
            return null;
        }

        return $widthValue / $heightValue;
    }

    /**
     * Converts a scalar token or theme-mod value to float.
     */
    private function toFloatOrNull(mixed $value): ?float
    {
        if (is_float($value) || is_int($value)) {
            return (float) $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
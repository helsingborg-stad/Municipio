<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\HeaderFactory;
use Municipio\Controller\Header\Helper\Enums;
use Municipio\Controller\Header\Helper\LogoScrollShrinkSettings;

/**
 * Class Flexible
 */
class Flexible
{
    private ?array $headerData = null;

    /**
     * Constructor.
     */
    public function __construct(
        private HeaderFactory $headerFactory,
        private LogoScrollShrinkSettings $logoScrollShrinkSettings
    ) {
    }

    /**
     * Gets the header data accessible in the view.
     *
     * @return array<string, mixed>
     */
    public function getHeaderData(): array
    {
        if ($this->headerData !== null) {
            return $this->headerData;
        }

        $upperHeader = $this->headerFactory->create(Enums::HEADER_KEY::UPPER->value);
        $lowerHeader = $this->headerFactory->create(Enums::HEADER_KEY::LOWER->value);

        $this->headerData = [
            'upper' => $upperHeader,
            'lower' => $lowerHeader,
        ];

        return $this->headerData;
    }

    /**
     * Get logotype scroll shrink settings from header context.
     *
     * @param bool $isCustomizePreview Whether current request is customize preview.
     * @return array{enabled: bool, overlapMultiplier: float, aspectRatio: ?float}
     */
    public function getLogoScrollShrinkSettings(bool $isCustomizePreview = false): array
    {
        $headerData = $this->getHeaderData();
        $lowerHeader = $headerData['lower'] ?? null;

        return $this->logoScrollShrinkSettings->resolve($lowerHeader, $isCustomizePreview);
    }
}

<?php

namespace Municipio\Api\Pdf;

use Municipio\Helper\FileConverters\FileConverterInterface;

interface PdfHelperInterface
{
    public function getFonts($styles, FileConverterInterface $fileConverter);
    public function getThemeMods();
    public function getCover(array $postTypes);
    public function getCoverFieldsForPostType(string $postType = "", bool $ranOnce = false);
    public function systemHasSuggestedDependencies(): bool;
}

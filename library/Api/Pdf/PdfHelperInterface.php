<?php

namespace Municipio\Api\Pdf;

interface PdfHelperInterface
{
    public function getFonts($styles);
    public function getThemeMods();
    public function getCover(array $postTypes);
    public function getCoverFieldsForPostType(string $postType = "", bool $ranOnce = false);
    public function systemHasSuggestedDependencies(): bool;
}

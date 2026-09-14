<?php

declare(strict_types=1);

namespace Modularity\Integrations\Component;

class ImageResolver extends \Municipio\Integrations\Component\ImageResolver
{
    /**
     * Apply backward-compatible Modularity and Municipio LQIP filters.
     *
     * @param string|null $lqipUrl
     * @param int $id
     * @param array $size
     * @param array $context
     * @return string|null
     */
    protected function filterLqipUrl(?string $lqipUrl, int $id, array $size, array $context): ?string
    {
        $lqipUrl = parent::filterLqipUrl($lqipUrl, $id, $size, $context);

        return apply_filters('Modularity/Component/Image/LqipUrl', $lqipUrl, $id, $size, $context);
    }
}

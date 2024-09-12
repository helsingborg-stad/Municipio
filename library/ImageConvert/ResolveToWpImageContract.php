<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\AddFilter;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;

class ResolveToWpImageContract implements Hookable
{
    public function __construct(private AddFilter $wpService, private ImageConvertConfig $config)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            $this->config->createFilterKey('imageDownsize'),
            [$this, 'resolveToWpImageContract'],
            $this->config->internalFilterPriority()->resolveToWpImageContract,
            1
        );
    }

    public function resolveToWpImageContract($image): false|array
    {
        if (!$image instanceof ImageContract) {
            return $image;
        }
        return [
            $image->getUrl(),
            $image->getWidth(),
            $image->getHeight(),
            true
        ];
    }
}

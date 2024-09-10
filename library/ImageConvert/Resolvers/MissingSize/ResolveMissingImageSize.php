<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Resolvers\MissingSize\ResolveMissingImageSizeInterface;

class ResolveMissingImageSize implements Hookable
{
    private $wpService;
    private ImageConvertConfig $config;
    private ResolveMissingImageSizeInterface $resolver;

    public function __construct($wpService, ImageConvertConfig $config)
    {
        $this->wpService  = $wpService;
        $this->config     = $config;

        $this->resolver = new ResolveMissingImageSizeByMeta($wpService);
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            $this->config->createFilterKey('imageDownsize'),
            [$this, 'resolveMissingImageSize'],
            $this->config->internalFilterPriority()->resolveMissingImageSize,
        );
    }
}
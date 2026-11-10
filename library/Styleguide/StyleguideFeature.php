<?php

namespace Municipio\Styleguide;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Styleguide\EnqueueStyleguideStyles\EnqueueStyleguideStyles;
use WpService\WpService;
use WpUtilService\WpUtilService;

class StyleguideFeature implements Hookable
{
    public function __construct(
        private WpService $wpService,
        private WpUtilService $wpUtilService,
    ) {}

    public function addHooks(): void
    {
        (new EnqueueStyleguideStyles($this->wpService))->addHooks();
        (new ApplyLayerToInlineStyles\ApplyLayerToInlineStyles($this->wpService))->addHooks();
        (new ApplyLayersToEnqueuedStyles\ApplyLayersToEnqueuedStyles($this->wpService))->addHooks();
        (new AddLayerOrderDefinitionToHead\AddLayerOrderDefinitionToHead($this->wpService))->addHooks();
        (new Customize\Customize(
            $this->wpService,
            $this->wpUtilService->enqueue(dirname(__DIR__, 2)),
        ))->addHooks();
    }
}

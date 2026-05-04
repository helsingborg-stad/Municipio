<?php

namespace Municipio\Styleguide;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Styleguide\EnqueueStyleguideStyles\EnqueueStyleguideStyles;
use WpService\WpService;

class StyleguideFeature implements Hookable
{
    public function __construct(
        private WpService $wpService,
    ) {}

    public function addHooks(): void
    {
        (new EnqueueStyleguideStyles($this->wpService))->addHooks();
        (new ApplyLayerToInlineStyles\ApplyLayerToInlineStyles($this->wpService))->addHooks();
        (new ApplyLayersToEnqueuedStyles\ApplyLayersToEnqueuedStyles($this->wpService))->addHooks();
        (new AddLayerOrderDefinitionToHead\AddLayerOrderDefinitionToHead($this->wpService))->addHooks();
        (new Customize\Customize($this->wpService))->addHooks();
    }
}

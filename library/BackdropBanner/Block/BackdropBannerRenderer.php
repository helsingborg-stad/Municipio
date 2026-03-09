<?php

namespace Municipio\BackdropBanner\Block;

class BackdropBannerRenderer
{
    public function render(array $attributes): string
    {
        $wrapperAttributes = get_block_wrapper_attributes([
            'class' => 'backdrop-banner',
        ]);

        return sprintf('<div %s></div>', $wrapperAttributes);
    }
}

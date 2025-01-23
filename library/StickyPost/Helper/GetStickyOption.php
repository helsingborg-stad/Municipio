<?php

namespace Municipio\StickyPost\Helper;

use Municipio\StickyPost\Config\StickyPostConfigInterface;
use WpService\Contracts\GetOption;

class GetStickyOption
{
    public function __construct(
        private StickyPostConfigInterface $stickyPostConfig, 
        private GetOption $wpService
    ) {  
    }

    public function getOptionKey(string $suffix): string
    {
        return $this->stickyPostConfig->getOptionKeyPrefix() . '_' . $suffix;
    }

        /**
     * Retrieves the sticky option for a given post type.
     *
     * @param string $postType The post type.
     * @return array The sticky option for the post type.
     */
    public function getOption(string $suffix): array
    {
        $option = $this->wpService->getOption(
            $this->getOptionKey($suffix),
            []
        );

        if (!is_array($option)) {
            $option = [];
        }

        return $option;
    }
}
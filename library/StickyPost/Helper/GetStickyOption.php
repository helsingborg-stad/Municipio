<?php

namespace Municipio\StickyPost\Helper;

use Municipio\StickyPost\Config\StickyPostConfigInterface;
use WpService\Contracts\GetOption;

/**
 * Represents a GetStickyOption class.
 *
 * This class is responsible for retrieving the sticky option for a given post type.
 */
class GetStickyOption
{
    private static array $optionsCache = [];

    /**
     * Constructor for the GetStickyOption class.
     *
     * @param StickyPostConfigInterface $stickyPostConfig The sticky post configuration.
     * @param GetOption $wpService The WP service.
     */
    public function __construct(
        private StickyPostConfigInterface $stickyPostConfig,
        private GetOption $wpService
    ) {
    }

    /**
     * Returns the option key for a given suffix.
     *
     * @param string $suffix The suffix to append to the option key.
     * @return string The option key.
     */
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
        $key = $this->getOptionKey($suffix);

        if (isset(self::$optionsCache[$key])) {
            return self::$optionsCache[$key];
        }

        $option = $this->wpService->getOption(
            $this->getOptionKey($suffix),
            []
        );

        if (!is_array($option)) {
            $option = [];
        }
        
        self::$optionsCache[$key] = $option;

        return $option;
    }
}

<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Header;

/**
 * Answers which components (menu item types) are placed in the rendered headers.
 */
class HeaderComponents
{
    /**
     * @param array<string, Header> $headers Headers keyed by header key (upper, lower).
     */
    public function __construct(private array $headers)
    {
    }

    /**
     * Check if a component is placed in any of the headers.
     */
    public function has(string $component): bool
    {
        foreach ($this->headers as $header) {
            if ($header instanceof Header && isset($header->getMenuItems()[$component])) {
                return true;
            }
        }

        return false;
    }
}

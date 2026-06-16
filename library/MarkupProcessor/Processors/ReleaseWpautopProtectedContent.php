<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\Content\WpAutopContentGuard\WpAutopContentGuard;
use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Class ReleaseWpautopProtectedContent
 *
 * Unwraps all elements wrapped in <pre class="wpautop-protected"> to prevent wpautop from adding <p> tags around them.
 * This is necessary to ensure that content that should not be modified by wpautop remains intact.
 * The processor should be used after all content has been processed by wpautop, and it will look for the specific class "wpautop-protected" to identify which content to unwrap.
 */
class ReleaseWpautopProtectedContent implements MarkupProcessorInterface
{
    public function __construct(private WpAutopContentGuard $wpAutopContentGuard)
    {
    }

    public function process(string $markup): string
    {
        return $this->wpAutopContentGuard->unlock($markup);
    }
}

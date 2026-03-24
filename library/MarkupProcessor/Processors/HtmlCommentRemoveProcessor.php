<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Class HtmlCommentRemoveProcessor
 *
 * This processor removes HTML comments from the markup unless WP_DEBUG is enabled.
 */
class HtmlCommentRemoveProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        if (defined('WP_DEBUG') && constant('WP_DEBUG') === true) {
            return $markup;
        }

        return preg_replace('/<!--(.|\s)*?-->/', '', $markup);
    }
}

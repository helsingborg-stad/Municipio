<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Class EmptyIdRemoveProcessor
 *
 * This processor is responsible for removing empty id attributes from the markup.
 * It extends the MarkupProcessor class and implements the process method to perform the removal.
 */
class EmptyIdRemoveProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        return preg_replace('/\sid=""/', '', $markup);
    }
}

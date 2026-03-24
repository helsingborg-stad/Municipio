<?php

namespace Municipio\MarkupProcessor;

interface MarkupProcessorInterface
{
    /**
     * Processes the given markup string and returns the modified version.
     *
     * @param string $markup The input markup to be processed.
     * @return string The processed markup.
     */
    public function process(string $markup): string;
}

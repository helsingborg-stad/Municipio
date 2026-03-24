<?php

namespace Municipio\MarkupProcessor;

use WpService\WpService;

class MarkupProcessor implements MarkupProcessorInterface
{
    public function __construct(
        private WpService $wpService,
    ) {}

    /**
     * Processes the given markup string and returns the modified version.
     *
     * @param string $markup The input markup to be processed.
     * @return string The processed markup.
     */
    public function process(string $markup): string
    {
        foreach ($this->getProcessors() as $processor) {
            $markup = $processor->process($markup);
        }

        return $markup;
    }

    private function getProcessors(): array
    {
        return [
            new Processors\TidyProcessor(),
            new Processors\CssMinifyProcessor(),
            new Processors\ScriptMinifyProcessor(),
            new Processors\HtmlCommentRemoveProcessor(),
            new Processors\EmptyIdRemoveProcessor(),
            new Processors\DeprecatedAttributesRemoveProcessor(),
            new Processors\FilterProcessor($this->wpService),
        ];
    }
}

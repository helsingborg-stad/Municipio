<?php

namespace Modularity\Module\Markdown\Providers;

use League\CommonMark\MarkdownConverter;

abstract class BaseProvider implements ProviderInterface
{
    abstract public function isValidProviderUrl(string $url): bool;

    abstract public function getExample(): string;

    abstract public function getName(): string;

    public function implementation(): null|MarkdownConverter
    {
        return null;
    }
}

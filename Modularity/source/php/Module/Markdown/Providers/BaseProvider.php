<?php 

namespace Modularity\Module\Markdown\Providers;

use League\CommonMark\MarkdownConverter;

abstract class BaseProvider implements ProviderInterface
{
    public abstract function isValidProviderUrl(string $url): bool;
    public abstract function getExample(): string;
    public abstract function getName(): string;

    public function implementation(): ?MarkdownConverter
    {
      return null;
    }
}
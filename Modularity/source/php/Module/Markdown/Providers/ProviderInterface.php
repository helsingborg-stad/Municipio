<?php

namespace Modularity\Module\Markdown\Providers;

use League\CommonMark\MarkdownConverter;

Interface ProviderInterface
{
  public function isValidProviderUrl(string $url): bool;
  public function getExample(): string;
  public function getName(): string;
  public function implementation(): ?MarkdownConverter;
}
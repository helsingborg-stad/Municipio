<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

use AcfService\Contracts\GetField;
use Municipio\EasyRead\Config\EasyReadConfigInterface;

final class AcfAlternativeContentRepository implements AlternativeContentRepositoryInterface
{
    public function __construct(
        private GetField $acfService,
        private EasyReadConfigInterface $config,
    ) {}

    public function hasAlternative(int $postId): bool
    {
        return (bool) $this->acfService->getField($this->config->enabledField(), $postId);
    }

    public function getAlternative(int $postId): string
    {
        $content = $this->acfService->getField($this->config->contentField(), $postId);
        return is_string($content) ? $content : '';
    }
}

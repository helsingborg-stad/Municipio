<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

interface AlternativeContentRepositoryInterface
{
    public function hasAlternative(int $postId): bool;

    public function getAlternative(int $postId): string;
}

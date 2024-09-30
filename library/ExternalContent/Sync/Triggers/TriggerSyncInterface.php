<?php

namespace Municipio\ExternalContent\Sync\Triggers;

interface TriggerSyncInterface
{
    public function trigger(string $postType, ?int $postId = null): void;
}

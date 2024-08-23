<?php

namespace Municipio\ExternalContent\Sync\Triggers;

interface TriggerInterface
{
    public function trigger(): void;
}

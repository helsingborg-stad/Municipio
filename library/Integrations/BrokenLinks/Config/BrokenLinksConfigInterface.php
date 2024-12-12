<?php

namespace Municipio\Integrations\BrokenLinks\Config;

interface BrokenLinksConfigInterface
{
    public function isEnabled(): bool;
    public function shouldRedirectToLoginPageWhenInternalContext(): bool;
}

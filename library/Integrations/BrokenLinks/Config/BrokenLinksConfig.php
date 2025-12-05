<?php

namespace Municipio\Integrations\BrokenLinks\Config;

class BrokenLinksConfig
{
    public function isEnabled(): bool
    {
        return class_exists('BrokenLinkDetector\App');
    }
    public function shouldRedirectToLoginPageWhenInternalContext(): bool
    {
        return (bool) get_option('options_municipio_redirect_to_login_when_internal_context', false) ?? false;
    }
}

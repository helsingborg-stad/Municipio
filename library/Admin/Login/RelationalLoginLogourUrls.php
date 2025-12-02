<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class RelationalLoginLogourUrls implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('login_url', array($this, 'filterUrl'), 10, 1);
        $this->wpService->addFilter('logout_url', array($this, 'filterUrl'), 10, 1);
    }

    public function filterUrl($url)
    {
        return (str_replace('&amp;', '&', $url));
    }
}

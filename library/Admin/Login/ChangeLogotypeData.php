<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class ChangeLogotypeData implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('login_headerurl', [$this, 'loginHeaderUrl']);
        $this->wpService->addFilter('login_headertext', [$this, 'loginHeaderText']);
    }

    public function loginHeaderUrl(): string
    {
        return 'https://getmunipio.com';
    }

    public function loginHeaderText(): string
    {
        return 'Powered by Municipio';
    }
}

<?php

namespace Municipio\Kulturkortet\Vitec;

use AcfService\Contracts\AddOptionsSubPage;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;

class VitecOptionsPage implements Hookable
{
    public function __construct(
        private AddAction&__ $wpService,
        private AddOptionsSubPage $acfService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'setupOptionsPage']);
    }

    public function setupOptionsPage(): void
    {
        $this->acfService->addOptionsSubPage([
            'page_title'  => $this->wpService->__('Vitec', 'municipio'),
            'menu_title'  => $this->wpService->__('Vitec', 'municipio'),
            'menu_slug'   => 'vitec-settings',
            'capability'  => 'manage_options',
            'parent_slug' => 'options-general.php',
        ]);
    }
}
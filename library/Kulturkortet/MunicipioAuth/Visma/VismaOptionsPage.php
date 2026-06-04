<?php

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use AcfService\Contracts\AddOptionsSubPage;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;

class VismaOptionsPage implements Hookable
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
            'page_title'  => $this->wpService->__('Visma', 'municipio'),
            'menu_title'  => $this->wpService->__('Visma', 'municipio'),
            'menu_slug'   => 'visma-settings',
            'capability'  => 'manage_options',
            'parent_slug' => 'options-general.php',
        ]);
    }
}
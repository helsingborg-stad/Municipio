<?php

namespace Municipio\SchemaData;

use AcfService\Contracts\AddOptionsSubPage;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;

class OptionsPage implements Hookable
{
    public function __construct(private AddAction $wpService, private AddOptionsSubPage $acfService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'addOptionsPage'));
    }

    public function addOptionsPage(): void
    {
        $this->acfService->addOptionsSubPage([
            'page_title'  => 'Schema.org settings',
            'menu_title'  => 'Schema.org settings',
            'menu_slug'   => 'schema-data',
            'capability'  => 'manage_options',
            'parent_slug' => 'options-general.php',
        ]);
    }
}

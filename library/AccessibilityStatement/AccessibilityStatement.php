<?php

namespace Municipio\AccessibilityStatement;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class AccessibilityStatement implements Hookable
{
    public function __construct(private WpService $wpService, private AcfService $acfService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('admin_menu', [$this, 'registerOptionsPage']);
    }

    public function registerOptionsPage()
    {
        $this->acfService->addOptionsPage([
            'page_title'      => __('Accessibility Statement', 'municipio'),
            'menu_title'      => __('Accessibility Statement', 'municipio'),
            'menu_slug'       => 'accessibility-statement',
            'capability'      => 'edit_posts',
            'redirect'        => true,
            'update_button'   => __('Update', 'municipio'),
            'updated_message' => __('The accessibility statement has been updated.', 'municipio'),
            'parent_slug'     => 'options-general.php',
        ]);
    }
}
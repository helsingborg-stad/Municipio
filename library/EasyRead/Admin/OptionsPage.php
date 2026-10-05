<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Admin;

use AcfService\Contracts\AddOptionsSubPage;
use WpService\Contracts\AddAction;
use WpService\Contracts\_x;

final class OptionsPage
{
    public function __construct(
        private AddAction&_x $wpService,
        private AddOptionsSubPage $acfService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'register']);
    }

    public function register(): void
    {
        $this->acfService->addOptionsSubPage([
            'page_title' => $this->wpService->_x('Easy reading settings', 'ACF', 'municipio'),
            'menu_title' => $this->wpService->_x('Easy reading', 'Easy reading settings', 'municipio'),
            'menu_slug' => 'easy-reading-options',
            'parent_slug' => 'options-general.php',
            'capability' => 'manage_options',
        ]);
    }
}

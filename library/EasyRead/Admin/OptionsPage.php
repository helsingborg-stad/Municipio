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
        // Register before Municipio imports its ACF field files on init (priority 10).
        $this->wpService->addAction('init', [$this, 'register'], 5);
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

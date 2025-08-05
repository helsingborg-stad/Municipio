<?php

namespace Municipio;

use Municipio\Upgrade;
use WP_CLI;
use WpService\Contracts\AddAction;

class WpCli {
    public function __construct(Upgrade $upgradeInstance, AddAction $wpService)
    {
        $wpService->addAction('cli_init', function () use ($upgradeInstance) {
            if (defined('WP_CLI') && WP_CLI) {
                if (function_exists('acf')) {
                    WP_CLI::add_command('municipio', $upgradeInstance);
                } else {
                    WP_CLI::error('ACF is not available.');
                }
            }
        });
    }
}
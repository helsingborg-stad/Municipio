<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use AcfService\Contracts\AddOptionsSubPage;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;

/** Registers the Kulturkortet Wallet settings page. */
class WalletOptionsPage implements Hookable
{
    /**
     * @param AddAction&__ $wpService WordPress action and translation services.
     * @param AddOptionsSubPage $acfService Registers ACF options pages.
     */
    public function __construct(
        private AddAction&__ $wpService,
        private AddOptionsSubPage $acfService,
    ) {}

    /** Registers the options-page hook. */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'setupOptionsPage']);
    }

    /** Creates the Wallet settings page under Settings. */
    public function setupOptionsPage(): void
    {
        $this->acfService->addOptionsSubPage([
            'page_title'  => $this->wpService->__('Kulturkortet Wallet', 'municipio'),
            'menu_title'  => $this->wpService->__('Kulturkortet Wallet', 'municipio'),
            'menu_slug'   => 'kulturkortet-wallet-settings',
            'capability'  => 'manage_options',
            'parent_slug' => 'options-general.php',
        ]);
    }
}

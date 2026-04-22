<?php

namespace Municipio\Chat\Module;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;

class RegisterChatModule implements Hookable
{
    public function __construct(
        private AddAction&AddFilter $wpService,
        private ChatConfigInterface $config,
    ) {}

    public function addHooks(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        if (function_exists('modularity_register_module')) {
            modularity_register_module(__DIR__, 'ChatModule');
        }

        $this->wpService->addFilter('/Modularity/externalViewPath', function (array $viewPaths): array {
            $viewPaths['mod-chat'] = __DIR__ . '/views';
            return $viewPaths;
        });

        $this->wpService->addAction('acf/init', function (): void {
            require_once __DIR__ . '/acf-fields.php';
        });
    }
}

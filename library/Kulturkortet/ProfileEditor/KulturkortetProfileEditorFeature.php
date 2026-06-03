<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\ProfileEditor;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Kulturkortet\MunicipioAuth\controller\secure\SecureMunicipioAuthConfig;
use Municipio\Kulturkortet\MunicipioAuth\controller\secure\SecureMunicipioAuthController;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigation;
use Municipio\Kulturkortet\MunicipioAuth\Visma\VismaAuthConfig;
use Municipio\Kulturkortet\MunicipioAuth\Visma\VismaAuthController;
use Municipio\Kulturkortet\Vitec\VitecService;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\IsWpError;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;

class KulturkortetProfileEditorFeature implements Hookable
{
    public function __construct(
        private AddAction&ApplyFilters&RegisterBlockType&HomeUrl&IsWpError&WpRemoteGet&WpRemoteRetrieveBody&WpCacheGet&WpCacheSet&__ $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
        $this->wpService->applyFilters('query_vars', ['ts_session_id', 'action']);
    }

    function registerBlock(): void
    {
        // https://make.wordpress.org/core/2026/03/03/php-only-block-registration/

        $this->wpService->registerBlockType(
            'kulturkortet/profile-editor',
            [
                'title' => $this->wpService->__('Kulturkortet Profile Editor', 'municipio'),
                'render_callback' => [$this, 'render'],
                'supports' => [
                    'autoRegister' => true,
                ],
                'attributes' => [
                    'ticketLinkLabel' => [
                        'label' => $this->wpService->__('Text for the ticket link', 'municipio'),
                        'type' => 'string',
                        'default' => $this->wpService->__('Show my ticket', 'municipio'),
                    ],
                    'ticketLink' => [
                        'label' => $this->wpService->__('Link to the ticket', 'municipio'),
                        'type' => 'string',
                        'default' => '',
                    ],
                ],
            ],
        );
    }

    function render(array $attributes): string
    {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $navigation = new MunicipioAuthNavigation($this->wpService);
        $vismaAuthController = VismaAuthController::createDefault($this->wpService, new VismaAuthConfig());

        $secureController = SecureMunicipioAuthController::createDefault(
            $vismaAuthController,
            new SecureMunicipioAuthConfig(),
        );

        $viewFactory = new KulturkortetProfileEditorAuthViewFactory($this->wpService, new VitecService($this->wpService), $attributes);

        return $secureController->render(
            $viewFactory,
            $navigation,
        );
    }
}

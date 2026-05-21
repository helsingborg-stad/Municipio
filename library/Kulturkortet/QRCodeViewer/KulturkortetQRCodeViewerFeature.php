<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\QRCodeViewer;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Kulturkortet\MunicipioAuth\controller\secure\SecureMunicipioAuthConfig;
use Municipio\Kulturkortet\MunicipioAuth\controller\secure\SecureMunicipioAuthController;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigation;
use Municipio\Kulturkortet\MunicipioAuth\Visma\VismaAuthConfig;
use Municipio\Kulturkortet\MunicipioAuth\Visma\VismaAuthController;
use Municipio\Kulturkortet\Vitec\VitecService;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddQueryArg;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\IsWpError;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemotePost;
use WpService\Contracts\WpRemoteRetrieveBody;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/*
 * Kulturkortet QR Code Viewer feature class
 */
class KulturkortetQRCodeViewerFeature implements Hookable
{
    /**
     * Constructor
     *
     * @param AddAction $wpService
     */
    public function __construct(
        private IsWpError&AddAction&ApplyFilters&RegisterBlockType&HomeUrl&WpRemoteGet&WpRemotePost&WpRemoteRetrieveBody&AddQueryArg&WpCacheGet&WpCacheSet $wpService,
        private EnqueueManagerInterface $enqueue,
    ) {}

    /**
     * Enable the Kulturkortet QR Code Viewer feature.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
        $this->wpService->addAction('litespeed_control_set_nocache', static fn() => 'cache disabled due to cookie usage');
        $this->wpService->applyFilters('query_vars', ['ts_session_id', 'action']);

        $this->enqueue->add('js/kulturkortet.js');
    }

    function registerBlock(): void
    {
        // https://make.wordpress.org/core/2026/03/03/php-only-block-registration/

        $this->wpService->registerBlockType(
            'kulturkortet/qr-code-viewer',
            [
                'title' => __('Kulturkortet QR Code Viewer', 'municipio'),
                'render_callback' => [$this, 'render'],
                'supports' => [
                    'autoRegister' => true,
                ],
                'attributes' => [
                    'profileLinkLabel' => [
                        'label' => __('Text på länk till profilsida', 'municipio'),
                        'type' => 'string',
                        'default' => __('Visa min profil', 'municipio'),
                    ],
                    'profileLink' => [
                        'label' => __('Länk till profilsida', 'municipio'),
                        'type' => 'string',
                        'default' => '',
                    ],
                ],
            ],
        );
    }

    function render(array $attributes): string
    {
        $navigation = new MunicipioAuthNavigation($this->wpService);
        $vismaAuthController = VismaAuthController::createDefault($this->wpService, new VismaAuthConfig());

        $secureController = SecureMunicipioAuthController::createDefault(
            $vismaAuthController,
            new SecureMunicipioAuthConfig(),
        );

        $viewFactory = new KulturkortetQRCodeViewerAuthViewFactory($this->wpService, new VitecService($this->wpService), $attributes);

        return $secureController->render(
            $viewFactory,
            $navigation,
        );
    }
}

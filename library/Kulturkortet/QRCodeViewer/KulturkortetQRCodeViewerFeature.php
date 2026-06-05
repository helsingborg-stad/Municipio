<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\QRCodeViewer;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Kulturkortet\MunicipioAuth\controller\secure\SecureMunicipioAuthConfig;
use Municipio\Kulturkortet\MunicipioAuth\controller\secure\SecureMunicipioAuthController;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigation;
use Municipio\Kulturkortet\MunicipioAuth\Visma\VismaAuthConfig;
use Municipio\Kulturkortet\MunicipioAuth\Visma\VismaAuthController;
use Municipio\Kulturkortet\Vitec\VitecConfig;
use Municipio\Kulturkortet\Vitec\VitecService;
use WpService\Contracts\AddAction;
use WpService\WpService;
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
        private WpService $wpService,
        private AcfService $acfService,
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

        $this->enqueue->add('js/kulturkortetQR.js');
        $this->enqueue->add('css/kulturkortetQR.css');
    }

    public function registerBlock(): void
    {
        // https://make.wordpress.org/core/2026/03/03/php-only-block-registration/

        $this->wpService->registerBlockType(
            'kulturkortet/qr-code-viewer',
            [
                'title' => $this->wpService->__('Kulturkortet QR Code Viewer', 'municipio'),
                'render_callback' => [$this, 'render'],
                'supports' => [
                    'autoRegister' => true,
                ],
                'attributes' => [
                    'profileLink' => [
                        'label' => $this->wpService->__('Link profile page', 'municipio'),
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
        $vismaAuthController = VismaAuthController::createDefault($this->wpService, new VismaAuthConfig($this->acfService));

        $secureController = SecureMunicipioAuthController::createDefault(
            $vismaAuthController,
            new SecureMunicipioAuthConfig(),
        );

        $viewFactory = new KulturkortetQRCodeViewerAuthViewFactory($this->wpService, new VitecService($this->wpService, new VitecConfig($this->acfService)), $attributes);

        return $secureController->render(
            $viewFactory,
            $navigation,
        );
    }
}

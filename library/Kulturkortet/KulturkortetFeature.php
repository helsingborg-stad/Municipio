<?php

namespace Municipio\Kulturkortet;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use WpUtilService\WpUtilService;

class KulturkortetFeature implements Hookable
{
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private WpUtilService $wpUtilService
    ) {}

    public function addHooks(): void
    {
        /**
         * Setup Visma options page
         */
        (new \Municipio\Kulturkortet\MunicipioAuth\Visma\VismaOptionsPage(
            $this->wpService,
            $this->acfService,
        ))->addHooks();

        /**
         * Setup Vitec options page
         */
        (new \Municipio\Kulturkortet\Vitec\VitecOptionsPage(
            $this->wpService,
            $this->acfService,
        ))->addHooks();

        /**
         * Setup Kulturkortet QR Code Viewer
         */
        (new \Municipio\Kulturkortet\QRCodeViewer\KulturkortetQRCodeViewerFeature(
            $this->wpService,
            $this->acfService,
            $this->wpUtilService->enqueue(),
        ))->addHooks();

        /**
         * Setup Kulturkortet Profile Editor
         */
        (new \Municipio\Kulturkortet\ProfileEditor\KulturkortetProfileEditorFeature(
            $this->wpService,
            $this->acfService,
        ))->addHooks();
    }
}
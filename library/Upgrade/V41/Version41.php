<?php

namespace Municipio\Upgrade\V41;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use Municipio\Customizer\Applicators\Types\NullApplicator;
use Municipio\Helper\AcfService;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Class Version41
 */
class Version41 implements VersionInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpGetCustomCss&WpUpdateCustomCssPost $wpService,
        private GetField&UpdateField $acfService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $customCss = $this->wpService->wpGetCustomCss();
        $acfCustomCss = $this->acfService->getField('custom_css_input', 'option');

        // var_dump($customCss);
        // die();

        $searchReplaceMap = [
            '--color-header-background' => '--c-header--background-color',
            '--color-primary' => '--color--primary',
            '--color-secondary' => '--color--secondary',
            '--color-breadcrumb-icon' => '--c-breadcrumb--color--background-contrast-muted',
            '--color-background' => '--color--background',
        ];

        $customCss = str_replace(array_keys($searchReplaceMap), array_values($searchReplaceMap), $customCss);
        $acfCustomCss = str_replace(array_keys($searchReplaceMap), array_values($searchReplaceMap), $acfCustomCss);

        $this->wpService->wpUpdateCustomCssPost($customCss);
        $this->acfService->updateField('custom_css_input', $acfCustomCss, 'option');
    }
}

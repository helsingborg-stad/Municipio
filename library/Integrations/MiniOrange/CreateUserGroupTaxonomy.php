<?php

namespace Municipio\Integrations\MiniOrange;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class CreateUserGroupTaxonomy implements Hookable
{
    public function __construct(private WpService $wpService, private MiniOrangeConfig $config)
    {
    }

  /**
   * Add hooks to register the user group taxonomy
   */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'registerUserGroupTaxonomy'));
    }

  /**
   * Register the user group taxonomy
   *
   * @return void
   */
    public function registerUserGroupTaxonomy(): void
    {
        $taxonomy = $this->config->getUserGroupTaxonomy();

        $this->wpService->registerTaxonomy(
            $taxonomy,
            'user',
            array(
            'label'        => $this->wpService->__('User Groups', 'municipio'),
            'hierarchical' => false,
            'public'       => false,
            )
        );
    }
}

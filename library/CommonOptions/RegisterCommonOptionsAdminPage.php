<?php

namespace Municipio\CommonOptions;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;

class RegisterCommonOptionsAdminPage implements Hookable
{

  public function __construct(private WpService $wpService, private AcfService $acfService)
  {
  }

  public function addHooks(): void
  {
    $this->wpService->addAction('admin_menu', [$this, 'addAdminPage']);
  }

  public function addAdminPage(): void
  {
    if(!$this->wpService->isMainSite()) {
      return;
    }
    
    $this->acfService->addOptionsPage([
      'page_title'  => $this->wpService->__('Common Options Options', 'municipio'),
      'menu_title'  => $this->wpService->__('Common Options', 'municipio'),
      'menu_slug'   => 'common-options',
      'capability'  => 'manage_options',
      'redirect'    => true,
      'update_button' => $this->wpService->__('Save', 'municipio'),
      'updated_message' => $this->wpService->__('Common options settings has been saved.', 'municipio'),
      'parent_slug' => 'options-general.php',
    ]);
  }
}
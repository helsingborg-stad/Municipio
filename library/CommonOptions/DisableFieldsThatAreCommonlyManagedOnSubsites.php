<?php

namespace Municipio\CommonOptions;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonOptions\CommonOptionsConfigInterface;

class DisableFieldsThatAreCommonlyManagedOnSubsites implements Hookable
{
  public function __construct(private WpService $wpService, private AcfService $acfService, private SiteSwitcher $siteSwitcher, private CommonOptionsConfigInterface $config)
  {
  }

  public function addHooks(): void
  {
    $this->wpService->addAction('acf/init', [$this, 'disableFields']);
  }

  /**
   * Disables fields that are commonly managed on subsites.
   * 
   * @return void
   */
  public function disableFields(): void
  {
    if(!$this->shouldDisableFields()) {
      return;
    }

    if ($acfFieldKeysToFilter = $this->config->getAcfFieldsToFilter()) {
      foreach ($acfFieldKeysToFilter as $key) {
        $this->wpService->addFilter('acf/load_field/key=' . $key, function ($field) {
          $field['disabled']     = true;
          $field['instructions'] = __('MANAGED FROM MAIN SITE: ', 'municipio') . $field['instructions'];
          return $field;
        });
      }
    }
  } 

  /**
   * Check if the fields should be disabled.
   * 
   * @return bool
   */
  private function shouldDisableFields(): bool
  {
    return !$this->wpService->isMainSite();
  }

}
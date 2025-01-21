<?php

namespace Municipio\CommonOptions;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\CommonOptions\CommonOptionsConfig;

class PopulateFieldGroupSelect implements Hookable
{
  public function __construct(private WpService $wpService, private AcfService $acfService, private CommonOptionsConfig $config)
  {
  }

  public function addHooks(): void
  {
    $this->wpService->addFilter(
      'acf/load_field/name=' . $this->config->getOptionsSelectFieldKey(), 
      [$this, 'populateFieldGroupSelect']
    );
  }

  public function populateFieldGroupSelect($field)
  {
    //Declare empty list
    $field['choices'] = [];

    //Get the field groups (connected to options pages)
    $fieldGroups = $this->acfService->getFieldGroups();
    $fieldGroups = array_filter($fieldGroups, [$this, 'filterOptionPages']);

    if ($fieldGroups) {
      foreach ($fieldGroups as $fieldGroup) {
        $field['choices'][$fieldGroup['key']] = $fieldGroup['title'];
      }
    }

    return $field;
  }

  /**
   * Filter out field groups that are not option pages.
   * 
   * @param array $fieldGroup
   * 
   * @return bool
   */
  private function filterOptionPages($fieldGroup): bool
  {
    foreach ($fieldGroup['location'] as $locations) {
      foreach ($locations as $location) {
          if (
              isset($location['param'], $location['value']) &&
              $location['param'] === 'options_page'
          ) {
              return true;
          }
      }
    }
    return false;
  }
}
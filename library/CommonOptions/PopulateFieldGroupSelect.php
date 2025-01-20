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
    $field['choices'] = [];

    $fieldGroups = $this->acfService->getFieldGroups();

    //Only options pages
    $fieldGroups = array_filter($fieldGroups, function ($fieldGroup) {
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
    });

    //Populate list
    if ($fieldGroups) {
      foreach ($fieldGroups as $fieldGroup) {
        $field['choices'][$fieldGroup['key']] = $fieldGroup['title'];
      }
    }

    return $field;
  }
}
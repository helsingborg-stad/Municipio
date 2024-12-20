<?php

namespace Municipio\Integrations\MiniOrange\Provider;

use Municipio\Integrations\MiniOrange\Provider\ProviderInterface;
use Mo_Saml_Options_Enum_Attribute_Mapping;

class AzureAdProvider implements ProviderInterface
{
  public function identifier(): string
  {
    return '';
  }
  public function getMap(): array
  {
    return [
      Mo_Saml_Options_Enum_Attribute_Mapping::ATTRIBUTE_FIRST_NAME => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
      Mo_Saml_Options_Enum_Attribute_Mapping::ATTRIBUTE_LAST_NAME => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
      Mo_Saml_Options_Enum_Attribute_Mapping::ATTRIBUTE_GROUP_NAME => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/companyname',
    ];
  }
}
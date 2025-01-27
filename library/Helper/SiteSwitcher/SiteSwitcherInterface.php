<?php

namespace Municipio\Helper\SiteSwitcher;

use Municipio\Helper\SiteSwitcher\Contracts\GetFieldFromSite;
use Municipio\Helper\SiteSwitcher\Contracts\GetOptionFromSite;
use Municipio\Helper\SiteSwitcher\Contracts\RunInSite;

interface SiteSwitcherInterface extends GetFieldFromSite, RunInSite, GetOptionFromSite
{
}

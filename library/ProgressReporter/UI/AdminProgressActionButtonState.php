<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\UI;

/**
 * Describes whether an admin progress action can be started.
 */
enum AdminProgressActionButtonState
{
    case Enabled;
    case Disabled;
}
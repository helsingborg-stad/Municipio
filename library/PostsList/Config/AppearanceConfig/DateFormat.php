<?php

declare(strict_types=1);

namespace Municipio\PostsList\Config\AppearanceConfig;

enum DateFormat: string
{
    case DATE = 'date';
    case TIME = 'time';
    case DATE_TIME = 'date-time';
    case DATE_BADGE = 'date-badge';
    case NONE = 'none';
}

<?php

namespace Municipio\AdminNotice;

enum AdminNoticeLevels: string
{
    case INFO    = 'info';
    case WARNING = 'warning';
    case ERROR   = 'error';
}

<?php

namespace Municipio\Helper\AdminNotices;

enum AdminNoticeType: string {
    case INFO = 'info';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';
}
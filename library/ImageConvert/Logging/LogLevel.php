<?php

namespace Municipio\ImageConvert\Logging;

enum LogLevel: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case ERROR = 'error';
    case DEBUG = 'debug';
}
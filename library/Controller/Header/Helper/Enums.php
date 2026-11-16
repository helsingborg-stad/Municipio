<?php

namespace Municipio\Controller\Header\Helper;

enum MenuItemAlignment: string
{
    case LEFT = 'left';
    case CENTER = 'center';
    case RIGHT = 'right';
}

enum HeaderBreakpoint: string
{
    case DESKTOP = 'desktop';
    case MOBILE = 'mobile';
}

enum HeaderKey: string
{
    case UPPER = 'upper';
    case LOWER = 'lower';
}

class Enums
{
    public const MENU_ITEM_ALIGNMENT = MenuItemAlignment::class;
    public const HEADER_BREAKPOINT = HeaderBreakpoint::class;
    public const HEADER_KEY = HeaderKey::class;
}
<?php

namespace Municipio\MirroredPost\Utils;

use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogIdInterface;
use Municipio\MirroredPost\Utils\IsMirroredPost\IsMirroredPostInterface;

interface MirroredPostUtilsInterface extends
    IsMirroredPostInterface,
    GetOtherBlogIdInterface
{
}

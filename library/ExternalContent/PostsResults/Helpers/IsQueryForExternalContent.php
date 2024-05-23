<?php

namespace Municipio\ExternalContent\PostsResults\Helpers;

use WP_Query;

interface IsQueryForExternalContent
{
    public function isQueryForExternalContent(WP_Query $query): bool;
}

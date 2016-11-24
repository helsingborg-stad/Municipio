<?php

namespace Intranet\Controller;

class TableOfContents extends \Intranet\Controller\BaseController
{
    public static $cacheKeyGroup = 'intranet-table-of-contents';

    public function init()
    {
        $this->data['tableOfContents'] = $this->getTableOfContents();

        $this->data['selectedDepartment'] = isset($_GET['department']) && !empty($_GET['department']) ? $_GET['department'] : null;
        $this->data['titleQuery'] = isset($_GET['title']) && !empty($_GET['title']) ? $_GET['title'] : null;
    }

    public function getTableOfContents()
    {
        $site = null;
        $search = null;

        if (isset($_GET['department']) && !empty($_GET['department']) && is_numeric($_GET['department'])) {
            $site = $_GET['department'];
        }

        if (isset($_GET['title']) && !empty($_GET['title'])) {
            $search = $_GET['title'];
        }

        $cacheKey = md5(serialize(array($site, $search)));
        $cache = wp_cache_get($cacheKey, self::$cacheKeyGroup);

        if ($cache) {
            return $cache;
        }

        $data = \Intranet\Theme\TableOfContents::get($site, $search);
        wp_cache_add($cacheKey, $data, self::$cacheKeyGroup, 3600*10);

        return $data;
    }
}

<?php

namespace Intranet\Theme;

class Breadcrumb
{
    public function __construct()
    {
        add_filter('Municipio/Breadcrumbs/Items', array($this, 'prependMainSite'), 10, 2);
        add_filter('Municipio/Breadcrumbs', '__return_true');
    }

    public function prependMainSite($items, $object)
    {

        //Prepend "portalen"
        $mainSiteBloginfo = get_blog_details(BLOG_ID_CURRENT_SITE);
        array_unshift($items, '
            <li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                <a itemprop="item" href="' . $mainSiteBloginfo->home. '" title="' . $mainSiteBloginfo->blogname . '">
                    <i class="pricon pricon-home"></i>
                    <span itemprop="name ">' . $mainSiteBloginfo->blogname . '</span><meta itemprop="position" content="0">
                </a>
            </li>');

        return $items;
    }
}

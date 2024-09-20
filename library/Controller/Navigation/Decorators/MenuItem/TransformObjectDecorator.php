<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem;

class TransformObjectDecorator implements MenuItemDecoratorInterface
{
    /**
     * Add post data on post array
     *
     * @param   array   $menuItems  The post array
     *
     * @return  array   $menuItems  The post array, with appended data
     */
    public function decorate(array $menuItems): array
    {
        //Move post_title to label key
        $menuItems['label']       = $menuItems['post_title'];
        $menuItems['id']          = (int) $menuItems['ID'];
        $menuItems['post_parent'] = (int) $menuItems['post_parent'];

        //Unset data not needed
        unset($menuItems['post_title']);
        unset($menuItems['ID']);

        //Sort & return
        return array_merge(
            array(
                'id'          => null,
                'post_parent' => null,
                'post_type'   => null,
                'active'      => null,
                'ancestor'    => null,
                'label'       => null,
                'href'        => null,
                'children'    => null
            ),
            $menuItems
        );
    }
}
<?php

namespace Municipio\Admin\Options;

class Theme
{
    public function __construct()
    {
        if (function_exists('acf_add_options_page')) {
            $themeOptionsCapability = 'administrator';
            $themeOptionsParent     = 'themes.php';

            acf_add_options_sub_page(array(
                'page_title'  => __('Theme Options', 'municipio'),
                'menu_title'  => __('Theme Options', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'redirect'    => false,
                'icon_url'    => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyNCAyMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjQgMjE7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxwYXRoIGQ9Ik04LjIsMTMuNUgyLjFDMS41LDEzLjcsMCwxNC40LDAsMTUuOGMwLDAuNSwwLjIsMC44LDAuNCwxaDQuNUw4LjIsMTMuNXoiLz4NCgk8cGF0aCBkPSJNMjEuOSwxMy41aC02LjFsMy40LDMuM2g0LjVjMC4yLTAuMiwwLjQtMC41LDAuNC0xQzI0LDE0LjQsMjIuNSwxMy43LDIxLjksMTMuNXoiLz4NCgk8cG9seWdvbiBwb2ludHM9IjkuNCw0LjQgOS40LDUuMyAxMCw1LjMgMTAsNC4zIDExLjYsNC4zIDExLjYsNS4zIDEyLjQsNS4zIDEyLjQsNC4zIDE0LDQuMyAxNCw1LjMgMTQuNiw1LjMgMTQuNiw0LjQgMTQuOSw0LjQgDQoJCTEyLDIuMSAxMiwyLjEgMTIsMi4xIDkuMSw0LjQgCSIvPg0KCTxwYXRoIGQ9Ik0xMiwyYzAuMSwwLDAuMy0wLjEsMC4zLTAuM2MwLTAuMS0wLjEtMC4zLTAuMy0wLjNjLTAuMSwwLTAuMywwLjEtMC4zLDAuM0MxMS43LDEuOCwxMS45LDIsMTIsMiIvPg0KCTxwYXRoIGQ9Ik0xMi41LDAuNWMwLDAtMC4xLDAtMC4yLDBsMCwwYzAsMCwwLDAtMC4xLDBjMCwwLDAsMCwwLDBjMCwwLDAtMC4xLDAtMC4yYzAtMC4xLDAuMS0wLjIsMC4xLTAuMkwxMi40LDBsLTAuMSwwDQoJCWMwLDAtMC4yLDAtMC4yLDBjLTAuMSwwLTAuMiwwLTAuMiwwbC0wLjEsMGwwLjEsMC4xYzAsMCwwLjEsMC4yLDAuMSwwLjJjMCwwLjEsMCwwLjEsMCwwLjJjMCwwLDAsMCwwLDBjMCwwLDAsMC0wLjEsMA0KCQljLTAuMSwwLTAuMiwwLTAuMiwwbC0wLjEsMHYwLjNjMCwwLjEsMCwwLjMsMCwwLjNsMCwwLjFsMC4xLDBjMCwwLDAuMSwwLDAuMS0wLjFjMCwwLDAuMSwwLDAuMSwwYzAsMCwwLDAsMCwwYzAsMCwwLDAuMSwwLDAuMQ0KCQljMCwwLjEtMC4xLDAuMy0wLjEsMC4zbDAsMC4xbDAsMGMwLjEtMC4xLDAuMi0wLjIsMC4zLTAuMmMwLjEsMCwwLjIsMC4xLDAuMywwLjJsMCwwbDAtMC4xYzAsMC0wLjEtMC4yLTAuMS0wLjNjMCwwLDAtMC4xLDAtMC4xDQoJCWMwLDAsMCwwLDAsMGMwLDAsMC4xLDAsMC4xLDBjMCwwLDAuMSwwLjEsMC4xLDAuMWwwLjEsMGwwLTAuMWMwLDAsMC0wLjIsMC0wLjNMMTIuNSwwLjVMMTIuNSwwLjVMMTIuNSwwLjV6Ii8+DQoJPHBhdGggZD0iTTMuNCw1LjNjMC4yLDAsMC4zLDAuMSwwLjMsMC4zQzMuNyw1LjksMy42LDYsMy40LDZDMy4yLDYsMy4xLDUuOSwzLjEsNS43QzMuMSw1LjUsMy4yLDUuMywzLjQsNS4zIi8+DQoJPHBvbHlnb24gcG9pbnRzPSIzLjYsNi4yIDMuMiw2LjIgMy4yLDcuMyAzLjYsNy4zIDMuNiw2LjIgCSIvPg0KCTxwYXRoIGQ9Ik0yMC42LDUuM2MwLjIsMCwwLjMsMC4xLDAuMywwLjNjMCwwLjItMC4xLDAuMy0wLjMsMC4zYy0wLjIsMC0wLjMtMC4xLTAuMy0wLjNDMjAuMyw1LjUsMjAuNCw1LjMsMjAuNiw1LjMiLz4NCgk8cG9seWdvbiBwb2ludHM9IjIwLjgsNi4yIDIwLjQsNi4yIDIwLjQsNy4zIDIwLjgsNy4zIDIwLjgsNi4yIAkiLz4NCgk8cG9seWdvbiBwb2ludHM9IjE1LjcsNy41IDE1LjcsMTAuMyAyMywxMC4zIDIwLjksNy41IAkiLz4NCgk8cG9seWdvbiBwb2ludHM9IjguNCw3LjUgMy4xLDcuNSAxLjEsMTAuMyA4LjQsMTAuMyAJIi8+DQoJPHBhdGggZD0iTTIuNCwxMC43djIuNGg2di0yLjRIMi40eiBNNC4zLDEyLjRIMy41di0wLjljMC4xLTAuNCwwLjYtMC40LDAuOCwwVjEyLjR6IE01LjgsMTIuNEg1di0wLjljMC4xLTAuNCwwLjYtMC40LDAuOCwwVjEyLjR6DQoJCSBNNy4zLDEyLjRINi41di0wLjljMC4xLTAuNCwwLjYtMC40LDAuOCwwVjEyLjR6Ii8+DQoJPHBhdGggZD0iTTE1LjcsMTAuN3YyLjRoNnYtMi40SDE1Ljd6IE0xNy41LDEyLjRoLTAuOHYtMC45YzAuMS0wLjQsMC42LTAuNCwwLjgsMFYxMi40eiBNMTksMTIuNGgtMC44di0wLjljMC4xLTAuNCwwLjYtMC40LDAuOCwwDQoJCVYxMi40eiBNMjAuNSwxMi40aC0wLjh2LTAuOWMwLjEtMC40LDAuNi0wLjQsMC44LDBWMTIuNHoiLz4NCgk8cGF0aCBkPSJNNy4zLDE2LjlDNy40LDE2LjksNy42LDE3LDcuOSwxN2wwLDBjMC42LDAsMC44LTAuMiwwLjgtMC4ybDAsMGwwLDBsMCwwbDIuNy01LjhMNy4zLDE2LjlDNy4zLDE2LjgsNy4zLDE2LjksNy4zLDE2LjkNCgkJTDcuMywxNi45eiIvPg0KCTxwYXRoIGQ9Ik05LjEsMTYuOWMwLjEsMCwwLjQsMC4xLDAuNywwLjFsMCwwYzAuNSwwLDAuOC0wLjIsMC45LTAuMmwwLDBjMCwwLDAsMCwwLDBsMCwwbDAsMGwxLTUuOUw5LjEsMTYuOQ0KCQlDOS4xLDE2LjksOS4xLDE2LjksOS4xLDE2LjlMOS4xLDE2Ljl6Ii8+DQoJPHBhdGggZD0iTTExLjEsMTYuOGMwLjEsMC4xLDAuNCwwLjEsMC45LDAuMWwwLDBjMC41LDAsMC44LTAuMSwwLjktMC4xbDAsMEwxMiwxMC43TDExLjEsMTYuOEMxMS4xLDE2LjgsMTEuMSwxNi44LDExLjEsMTYuOA0KCQlMMTEuMSwxNi44eiIvPg0KCTxwYXRoIGQ9Ik01LjMsMTYuOGMwLjEsMC4xLDAuMywwLjEsMC41LDAuMWwwLDBjMC40LDAsMC44LTAuMSwwLjktMC4ybDAsMGMwLjEsMCwwLjEsMCwwLjEsMGwwLDBsMCwwbDQuMS01LjVMNS4zLDE2LjgNCgkJQzUuMywxNi44LDUuMywxNi44LDUuMywxNi44TDUuMywxNi44eiIvPg0KCTxwYXRoIGQ9Ik0xNi43LDE2LjlDMTYuNywxNi45LDE2LjgsMTYuOCwxNi43LDE2LjlMMTIuNiwxMWwyLjcsNS44bDAsMGwwLDBsMCwwYzAsMCwwLjMsMC4yLDAuOCwwLjJsMCwwDQoJCUMxNi40LDE3LDE2LjYsMTYuOSwxNi43LDE2LjlMMTYuNywxNi45eiIvPg0KCTxwYXRoIGQ9Ik0xNC45LDE2LjlDMTQuOSwxNi45LDE0LjksMTYuOSwxNC45LDE2LjlsLTIuNi02bDEsNS45bDAsMGwwLDBjMCwwLDAsMCwwLDBsMCwwYzAuMSwwLDAuNCwwLjIsMC45LDAuMmwwLDANCgkJQzE0LjUsMTcsMTQuNywxNi45LDE0LjksMTYuOUwxNC45LDE2Ljl6Ii8+DQoJPHBhdGggZD0iTTE4LjcsMTYuOEMxOC43LDE2LjgsMTguNywxNi44LDE4LjcsMTYuOEwxMywxMS4ybDQuMSw1LjVsMCwwbDAsMGMwLDAsMCwwLDAuMSwwbDAsMGMwLjIsMC4xLDAuNiwwLjIsMC45LDAuMmwwLDANCgkJQzE4LjQsMTYuOSwxOC42LDE2LjksMTguNywxNi44TDE4LjcsMTYuOHoiLz4NCgk8cGF0aCBkPSJNOC43LDYuN1YxM2wzLjEtM2wtMC41LTAuNUwxMiw4LjhsMC43LDAuN2wtMC41LDAuNWwzLjEsM1Y2LjdIOC43eiBNMTAuOSw4LjNoLTAuOFY3LjRjMC4xLTAuNCwwLjYtMC40LDAuOCwwVjguM3oNCgkJIE0xMi40LDguM2gtMC44VjcuNGMwLjEtMC40LDAuNi0wLjQsMC44LDBWOC4zeiBNMTMuOSw4LjNoLTAuOFY3LjRjMC4xLTAuNCwwLjYtMC40LDAuOCwwVjguM3oiLz4NCgk8cG9seWdvbiBwb2ludHM9IjguMSw2LjMgMTUuOSw2LjMgMTUuOSw0LjcgMTUsNC43IDE1LDUuNiAxMy42LDUuNiAxMy42LDQuNyAxMi44LDQuNyAxMi44LDUuNiAxMS4yLDUuNiAxMS4yLDQuNyAxMC40LDQuNyANCgkJMTAuNCw1LjYgOSw1LjYgOSw0LjcgOC4xLDQuNyA4LjEsNi4zIAkiLz4NCgk8cGF0aCBkPSJNNS43LDIxdi0zLjhjLTAuMiwwLTAuNC0wLjEtMC42LTAuMkgwLjZWMjFINS43eiBNNC4zLDE4LjRjMC4xLTAuNCwwLjYtMC40LDAuOCwwdjAuOUg0LjNWMTguNHogTTIuOCwxOC40DQoJCWMwLjEtMC40LDAuNi0wLjQsMC44LDB2MC45SDIuOFYxOC40eiBNMS4zLDE4LjRjMC4xLTAuNCwwLjYtMC40LDAuOCwwdjAuOUgxLjNWMTguNHoiLz4NCgk8cGF0aCBkPSJNMTcuOSwyMXYtMy44Yy0wLjQsMC0wLjctMC4xLTAuOC0wLjJjLTAuMiwwLjEtMC41LDAuMy0xLDAuM2MwLDAsMCwwLDAsMGMtMC41LDAtMC44LTAuMS0wLjktMC4yYy0wLjIsMC4xLTAuNSwwLjItMSwwLjINCgkJYy0wLjUsMC0wLjgtMC4yLTEtMC4yYy0wLjIsMC4xLTAuNSwwLjItMS4yLDAuMmMtMC43LDAtMS0wLjEtMS4yLTAuMmMtMC4yLDAuMS0wLjUsMC4yLTEsMC4yYy0wLjUsMC0wLjgtMC4xLTEtMC4yDQoJCWMtMC4xLDAuMS0wLjUsMC4yLTAuOSwwLjJjLTAuNSwwLTAuOC0wLjItMS0wLjNjLTAuMiwwLjEtMC41LDAuMi0wLjgsMC4yVjIxSDE3Ljl6IE0xNS43LDE4LjdjMC4yLTAuNSwwLjktMC41LDEuMSwwdjEuNWgtMS4xDQoJCVYxOC43eiBNMTMuNSwxOC41YzAuMi0wLjcsMS4xLTAuNywxLjMsMHYxLjdoLTEuM1YxOC41eiBNMTEuMywxOC41YzAuMi0wLjcsMS4xLTAuNywxLjMsMHYxLjdoLTEuM1YxOC41eiBNOS4xLDE4LjUNCgkJYzAuMi0wLjcsMS4xLTAuNywxLjMsMHYxLjdIOS4xVjE4LjV6IE03LjIsMTguN2MwLjItMC41LDAuOS0wLjUsMS4xLDB2MS41SDcuMlYxOC43eiIvPg0KCTxwYXRoIGQ9Ik0yMy40LDIxdi0zLjloLTQuNWMtMC4xLDAuMS0wLjMsMC4xLTAuNiwwLjJWMjFIMjMuNHogTTIxLjksMTguNGMwLjEtMC40LDAuNi0wLjQsMC44LDB2MC45aC0wLjhWMTguNHogTTIwLjQsMTguNA0KCQljMC4xLTAuNCwwLjYtMC40LDAuOCwwdjAuOWgtMC44VjE4LjR6IE0xOC45LDE4LjRjMC4xLTAuNCwwLjYtMC40LDAuOCwwdjAuOWgtMC44VjE4LjR6Ii8+DQo8L2c+DQo8L3N2Zz4NCg=='
            ));

            add_action(
                'init',
                function () use ($themeOptionsParent, $themeOptionsCapability) {
                    if (function_exists('get_field') && get_field('theme_mode', 'options') >= 2) {
                        acf_add_options_sub_page(array(
                            'page_title'  => __('Customizer', 'municipio'),
                            'menu_title'  => __('Customizer', 'municipio'),
                            'parent_slug' => $themeOptionsParent,
                            'capability'  => $themeOptionsCapability,
                            'menu_slug'   => 'acf-options-cstomizer'
                        ));
                    }
                }
            );

            acf_add_options_sub_page(array(
                'page_title'  => __('Content', 'municipio'),
                'menu_title'  => __('Content', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'acf-options-content'
            ));

            acf_add_options_sub_page(array(
                'page_title'  => __('Search', 'municipio'),
                'menu_title'  => __('Search', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'acf-options-search'
            ));

            acf_add_options_sub_page(array(
                'page_title'  => 'Google Translate',
                'menu_title'  => 'Google Translate',
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'acf-options-google-translate'
            ));

            acf_add_options_sub_page(array(
                'page_title'  => __('Content editor', 'municipio'),
                'menu_title'  => __('Content editor', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'acf-options-content-editor'
            ));

            acf_add_options_sub_page(array(
                'page_title'  => __('Custom Post Types', 'municipio'),
                'menu_title'  => __('Post Types', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'acf-options-post-types'
            ));

            acf_add_options_sub_page(array(
                'page_title'  => __('Custom Taxonomies', 'municipio'),
                'menu_title'  => __('Taxonomies', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'acf-options-taxonomies'
            ));

            acf_add_options_sub_page(array(
                'page_title'  => __('Custom CSS/JS Editor', 'municipio'),
                'menu_title'  => __('Custom CSS/JS', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'acf-options-css'
            ));

            acf_add_options_sub_page(array(
                'page_title'  => __('PDF Generator Settings', 'municipio'),
                'menu_title'  => __('PDF Generator Settings', 'municipio'),
                'parent_slug' => $themeOptionsParent,
                'capability'  => $themeOptionsCapability,
                'menu_slug'   => 'pdf-generator-settings'
            ));
        }
    }
}

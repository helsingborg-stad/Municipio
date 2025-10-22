<?php

if (!function_exists('modularity_register_module')) {
    function modularity_register_module($path, $name)
    {
        add_filter('Modularity/Modules', function ($modules) use ($path, $name) {
            $modules[$path] = $name;
            return $modules;
        });
    }
}

if (!function_exists('modularity_decode_icon')) {
    function modularity_decode_icon($data)
    {
        if (!empty($data['menu_icon'])) {
            if (isset($data['menu_icon_auto_import']) && $data['menu_icon_auto_import'] === true) {
                return $data['menu_icon'];
            } else {
                $data = explode(',', $data['menu_icon']);

                if (strpos($data[0], 'svg') !== false) {
                    return base64_decode($data[1]);
                }

                return '<img src="' . base64_decode($data[1]) . '">';
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>';
    }
}

/**
 * Get a posts featured image thumbnail by post id
 * @param  int|null $post_id Post id or null
 * @return string            Thumbnail url
 */
if (!function_exists('get_thumbnail_source')) {
    function get_thumbnail_source($post_id = null)
    {
        $id = get_post_thumbnail_id($post_id);
        $src = wp_get_attachment_image_srcset($id, 'medium', true);

        if (!$src) {
            $src = wp_get_attachment_url($id);
            $src = $src;
        }

        return $src;
    }
}

if (!function_exists('municipio_to_aspect_ratio')) {
    function municipio_to_aspect_ratio($ratio, $size)
    {
        $ratio = explode(':', $ratio);

        $width = round($size[0]);
        $height = round(($width / $ratio[0]) * $ratio[1]);

        return array($width, $height);
    }
}

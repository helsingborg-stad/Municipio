<?php 

namespace Municipio\Helper;

/**
 * Provides helpers to enqueue scripts and styles with optional module and localization support.
 */
class Enqueue {

    /**
     * Enqueue a script or style with optional module type and localization.
     *
     * @param string $handle The handle name for the script or style.
     * @param string $src The source URL of the script or style.
     * @param array $deps An array of dependencies for the script or style.
     * @param bool $module Whether to add the "module" attribute to the script tag.
     * @param array $localize Optional localization data with keys 'object_name' and 'data'.
     * @return void
     * @throws \InvalidArgumentException If localization data is invalid.
     */
    public static function enqueue(string $handle, string $src, array $deps = [], bool $module = false, $localize = []) {

        $func = self::getRegisterEnqeueFunctions(self::getFileType($src));
        $src  = self::getAssetUrl($src);

        $func['register']($handle, $src, $deps);

        if (!empty($localize) && isset($func['localize'])) {
            if (!is_array($localize) || !isset($localize['object_name']) || !isset($localize['data'])) {
                throw new \InvalidArgumentException('Localize data must be an array with "object_name" and "data" keys.');
            }
            $func['localize']($handle, $localize['object_name'], $localize['data']);
        }

        if ($module === true) {
          self::addAttributesToScriptTag($handle, ['type' => 'module']);
        }

        $func['enqueue']($handle);
    }

    /**
     * Get the register, enqueue, and optional localize functions based on file type.
     *
     * @param string $type The file type (e.g. 'js' or 'css').
     * @return array Associative array with keys 'register', 'enqueue', and optionally 'localize' containing callable functions.
     * @throws \InvalidArgumentException If an invalid type is provided.
     */
    private static function getRegisterEnqeueFunctions($type) {
        
      if ($type === 'js') {
            return [
              'register'  => fn($handle, $src, $deps) => wp_register_script($handle, $src, $deps, false, true), 
              'enqueue'   => fn($handle) => wp_enqueue_script($handle),
              'localize'  => fn($handle, $object_name, $data) => wp_localize_script($handle, $object_name, $data)
            ];
        }

        if ($type === 'css') {
            return [
              'register'  => fn($handle, $src, $deps) => wp_register_style($handle, $src, $deps, false), 
              'enqueue'   => fn($handle) => wp_enqueue_style($handle)
            ];
        }
        
        throw new \InvalidArgumentException('Invalid type provided. Use "js" or "css".');
    }

    /**
     * Add attributes to the script tag for a given handle.
     *
     * @param string $handle The handle of the script to modify.
     * @param array $attributes Key-value pairs of attributes to add to the script tag.
     * @return void
     */
    private static function addAttributesToScriptTag(string $handle, array $attributes): void {
        add_filter('script_loader_tag', function($tag, $tag_handle) use ($handle, $attributes) {
            if ($tag_handle === $handle) {
                foreach ($attributes as $key => $value) {
                    $tag = str_replace(' src=', sprintf(' %s="%s" src=', esc_attr($key), esc_attr($value)), $tag);
                }
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Get the file type extension from the source string.
     *
     * @param string $src The source file path or URL.
     * @return string The file extension (e.g. 'js' or 'css').
     * @throws \InvalidArgumentException If the file extension is unsupported.
     */
    private static function getFileType(string $src): string {
      $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION) ?? '');
      if (!in_array($ext, ['js', 'css'], true)) {
          throw new \InvalidArgumentException("Unsupported file extension: {$ext}");
      }
      return $ext;
    }

    /**
     * Get the URL of an asset with cache busting.
     *
     * @param string $file The file name to get the URL for.
     * @return string The URL of the asset.
     */
    private static function getAssetUrl(string $src): string {
        return get_template_directory_uri() .
         ASSETS_DIST_PATH . 
         \Municipio\Helper\CacheBust::name($src);
    }

  }
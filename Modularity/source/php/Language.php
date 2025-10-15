<?php

/**
 * Language
 * This class handles the language of the modules, and adds 
 * the lang attribute to the HTML element if it differs from 
 * the site's language. 
 * 
 * It also detects the language of the module automatically 
 * and updates the language indicator in the module's settings.
 * 
 * @package Modularity
 */

namespace Modularity;

use LanguageDetector\LanguageDetector;

class Language
{
    public function __construct()
    {
        // Lang attribute option
        add_filter('Modularity/Display/BeforeModule', array($this, 'addLangAttribute'), 10, 4);

        // Detect language of module
        add_filter('wp_after_insert_post', array($this, 'autoDetectLanguage'), 50, 4);

        //Make auto a selectable option in the language field
        add_filter('acf/load_field/key=field_636e42408367e', array($this, 'appendAutoLangOption'));
    }

    /**
     * Appends the auto detected language to the language field
     * @param array $field The field array
     * @return array The modified field array
     */
    public function appendAutoLangOption(array $field): array
    {
        $field['choices'] = array_merge([
            'auto' => __('Auto detected language', 'modularity')
        ], $field['choices']);
        return $field;
    }

    /**
     * Detects the language of the module and adds it to the HTML element
     *
     * @param int $post_id The ID of the post
     * @param WP_Post $post The post object
     * @param array $update The update array
     * @param WP_Post $post_before The post object before the update
     *
     * @return void 
     */
    public function autoDetectLanguage($post_id, $post, $update, $post_before): bool {

        if ($this->isModularityModule($post_id) === false) {
          return false;
        }

        //Render by using the module's render method
        $module = $this->getRenderedModule($post_id);

        //Get the language of the module
        $detector = new LanguageDetector();
        $langCode = (string) $detector->evaluate($module)->getLanguage();

        //Update the detected language of the module
        if ($langCode) {
          update_post_meta($post_id, 'detected_lang', $langCode);
          return true;
        }

        //Delete the detected language if it's not set
        delete_post_meta($post_id, 'detected_lang');
        return false;
    }

    /**
     * Adds the `lang` attribute to the module's HTML element if it differs from the site's language
     *
     * @param string beforeModule The HTML of the module before it's been modified.
     * @param array args the arguments passed to the module
     * @param string moduleType the type of module (e.g. 'acf_module')
     * @param int moduleId the id of the module
     *
     * @return string the $beforeModule content with the lang attribute added.
     */
    public function addLangAttribute(string $beforeModule, array $args, string $moduleType, int $moduleId): string
    {
        $pageId                 = \Modularity\Helper\Post::getPageID();
        $siteLanguage           = get_bloginfo('language');
        $moduleLanguage         = get_post_meta($moduleId, 'lang', true);
        $pageLanguage           = get_post_meta($pageId, 'lang', true);

        //Get auto language
        if ($moduleLanguage == 'auto') {
            $moduleLanguage = get_post_meta($moduleId, 'detected_lang', true);
        }

        $languageDiff   =   array_map('strtolower', [$siteLanguage, $moduleLanguage, $pageLanguage]);
        $languageDiff   =   array_map(
            function ($value) use ($siteLanguage) {
                return $value ?: strtolower($siteLanguage);
            },
            $languageDiff
        );

        if (count(array_unique($languageDiff)) != 1) {
            $moduleLanguage = substr($moduleLanguage, 0, 2);

            return $this->addLangAttributeToString(
                $beforeModule, 
                $moduleLanguage
            );
        }
        return $beforeModule;
    }

    /**
     * Adds an attribute to a string
     *
     * @param string $html The HTML string to add the attribute to
     * @param string $lang The language code to add
     * @param string $attr The attribute to add
     *
     * @return string The modified HTML string
     */
    function addLangAttributeToString($html, $lang): string
    {
        $pattern        = '/(<div\s+id="mod-[^"]+")/';
        $replacement    = '$1 lang="' . $lang . '"';
        return preg_replace($pattern, $replacement, $html);
    }


    /**
     * Check if a post is a modularity module
     * 
     * @param int $postId The ID of the post
     * 
     * @return bool
     */
    private function isModularityModule($postId): bool
    {
      return substr(get_post_type($postId), 0, 4) == 'mod-';
    }

    /**
     * Get the rendered module
     * 
     * @param int $postId The ID of the post
     * 
     * @return string
     */
    private function getRenderedModule($postId): string
    {
        $module = do_shortcode('[modularity id="' . $postId . '"]');
        $module = strip_tags($module);
        $module = trim($module);
        $module = preg_replace('/\s+/', ' ', $module);
        return $module;
    }

}
<?php

declare(strict_types=1);


namespace Municipio\PostTypeDesign;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostType;

/**
 * Class SetDesigns
 *
 * This class represents a set of designs for a specific post type.
 */
class SetDesigns implements Hookable
{
    /**
     * @var string|false $postType Current post type.
     */
    private $postType = false;

    public static array|null $cache = null;

    /**
     * Class SetDesigns
     *
     * Represents a class that sets the designs for a post type.
     */
    public function __construct(
        private string $optionName,
        private AddFilter&AddAction&GetPostType&GetOption $wpService
    ) {
    }

    /**
     * Adds hooks for setting designs.
     *
     * This method adds hooks to modify the theme mods and custom CSS for setting designs.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('option_theme_mods_municipio', [$this, 'setDesign'], 10, 2);
        $this->wpService->addFilter('wp_get_custom_css', [$this, 'setCss'], 10, 2);
        $this->wpService->addAction('wp_head', [$this, 'addInlineCss']);
        $this->wpService->addFilter('Municipio/Customizer/CacheKeySuffix', [$this, 'setUniqueCustomizerCacheSuffix'], 10, 3);
    }

    /**
     * Retrieves the design option from the WordPress service.
     *
     * @return array The design option.
     */
    public function getDesignOption(): array
    {
        if (!is_null(static::$cache)) {
            return static::$cache;
        }

        $designOption = $this->wpService->getOption($this->optionName, []);
        static::$cache = $designOption;

        return static::$cache;
    }

    /**
     * Sets the unique customizer cache suffix.
     *
     * This method sets the unique customizer cache suffix based on the provided parameters.
     * If the design option for the given post type is empty or the post type is false, it returns the default value.
     * Otherwise, it returns the post type.
     *
     * @param string $defaultValue The default value to return if the design option is empty or the post type is false.
     * @param bool $isCustomizerPreview Indicates if the customizer is in preview mode.
     * @param string|false $postType The post type to set the customizer cache suffix for.
     * @return string The unique customizer cache suffix.
     */
    public function setUniqueCustomizerCacheSuffix(string $defaultValue, bool $isCustomizerPreview, string|false $postType)
    {
        if (!$postType || empty($this->getDesignOption()[$postType])) {
            return $defaultValue;
        }

        return $postType;
    }

    /**
     * Sets the CSS for a specific post type design.
     *
     * @param string $css The CSS to be set.
     * @param string $stylesheet The name of the stylesheet.
     * @return string The updated CSS for the post type design.
     */
    public function setCss(string $css, string $stylesheet): string
    {
        $this->postType = $this->postType ?: $this->wpService->getPostType();

        if (empty($this->postType) || empty($this->getDesignOption()[$this->postType]['css'])) {
            return $css;
        }

        return $this->getDesignOption()[$this->postType]['css'];
    }

    /**
     * Sets the design for a given value and option.
     *
     * @param mixed $value The value to set the design for.
     * @param string $option The option to set the design for.
     * @return mixed The modified value with the design applied.
     */
    public function setDesign(mixed $value, string $option): mixed
    {
        $this->postType = $this->postType ?: $this->wpService->getPostType();

        if (empty($this->postType) || empty($this->getDesignOption()[$this->postType]['design'])) {
            return $value;
        }

        $design = $this->getDesignOption()[$this->postType]['design'];
        return is_array($value) ? array_replace($value, (array) $design) : $design;
    }

    /**
     * Adds inline CSS to the page.
     *
     * This method retrieves the 'inlineCss' option from the WordPress service and adds it as inline CSS to the page.
     * If the 'inlineCss' option is empty, no CSS is added.
     *
     * @return void
     */
    public function addInlineCss(): void
    {
        if (empty($this->getDesignOption()['inlineCss'])) {
            return;
        }

        ?>
            <style id="post-type-design-inline" type="text/css">
                <?php echo $this->getDesignOption()['inlineCss'] ?>
            </style>
        <?php
    }
}

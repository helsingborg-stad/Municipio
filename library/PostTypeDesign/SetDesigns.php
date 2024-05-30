<?php

namespace Municipio\PostTypeDesign;

use Municipio\HooksRegistrar\Hookable;
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

    /**
     * Class SetDesigns
     *
     * Represents a class that sets the designs for a post type.
     */
    public function __construct(private string $optionName, private AddFilter&GetPostType&GetOption $wpService)
    {
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
        $this->wpService->addFilter("option_theme_mods_municipio", array($this, 'setDesign'), 10, 2);
        $this->wpService->addFilter('wp_get_custom_css', array($this, 'setCss'), 10, 2);
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

        if (empty($this->postType) || empty($this->wpService->getOption($this->optionName)[$this->postType]['css'])) {
            return $css;
        }

        return $this->wpService->getOption($this->optionName)[$this->postType]['css'];
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

        if (empty($this->postType) || empty($this->wpService->getOption($this->optionName)[$this->postType]['design'])) {
            return $value;
        }

        $design = $this->wpService->getOption($this->optionName)[$this->postType]['design'];
        $value  = is_array($value) ? array_replace($value, (array) $design) : $design;

        return $value;
    }
}

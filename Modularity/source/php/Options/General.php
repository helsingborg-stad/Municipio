<?php

namespace Modularity\Options;

class General extends \Modularity\Options
{
    public function __construct()
    {
        $this->register(
            $pageTitle = __("Modularity Options", "modularity"),
            $menuTitle = __("Options", "modularity"),
            $capability = "administrator",
            $menuSlug = "modularity-options",
            $iconUrl =
                "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1NC44NDkiIGhlaWdodD0iNTQuODQ5IiB2aWV3Qm94PSIwIDAgNTQuODQ5IDU0Ljg0OSI+PHBhdGggZD0iTTU0LjQ5NyAzOS42MTRsLTEwLjM2My00LjQ5LTE0LjkxNyA1Ljk2OGMtLjUzNy4yMTQtMS4xNjUuMzItMS43OTMuMzItLjYyNyAwLTEuMjU0LS4xMDUtMS43OS0uMzJsLTE0LjkyLTUuOTY3TC4zNSAzOS42MTVjLS40Ny4yMDItLjQ2Ni41MjMuMDEuNzE1bDI2LjIgMTAuNDhjLjQ3Ny4xOSAxLjI1LjE5IDEuNzMgMGwyNi4xOTgtMTAuNDhjLjQ3Ni0uMTkuNDgtLjUxMy4wMS0uNzE2eiIvPjxwYXRoIGQ9Ik01NC40OTcgMjcuNTEybC0xMC4zNjQtNC40OS0xNC45MTYgNS45NjVjLS41MzYuMjE1LTEuMTY1LjMyLTEuNzkyLjMyYTQuODk4IDQuODk4IDAgMCAxLTEuNzkzLS4zMkwxMC43MTQgMjMuMDIuMzUgMjcuNTEzYy0uNDcuMjAzLS40NjYuNTIzLjAxLjcxNmwyNi4yIDEwLjQ3OGMuNDc3LjE5IDEuMjUuMTkgMS43MyAwbDI2LjE5OC0xMC40OGMuNDc2LS4xOS40OC0uNTEuMDEtLjcxNHoiLz48cGF0aCBkPSJNLjM2IDE2LjEyNWwxMy42NjMgNS40NjUgMTIuNTM3IDUuMDE1Yy40NzcuMTkgMS4yNS4xOSAxLjczIDBsMTIuNTQtNS4wMTYgMTMuNjU4LTUuNDY0Yy40NzctLjE5LjQ4LS41MS4wMS0uNzE2TDI4LjI3OCA0LjA0OGMtLjQ3Mi0uMjA0LTEuMjM3LS4yMDQtMS43MSAwTC4zNTIgMTUuNDFjLS40Ny4yMDQtLjQ2Ni41MjUuMDEuNzE1eiIvPjwvc3ZnPg==",
            $position = 1
        );

        // Add search page modules link to Moduliarty
        add_action("admin_menu", function () {
            if (function_exists("add_submenu_page")) {
                add_submenu_page(
                    "modularity",
                    __("Search page modules", "modularity"),
                    __("Search page modules", "modularity"),
                    "edit_posts",
                    "options.php?page=modularity-editor&id=search"
                );
            }
        });
    }

    /**
     * Adds meta boxes to the general options page
     * @return void
     */
    public function addMetaBoxes()
    {
        // Publish
        add_meta_box(
            "modularity-mb-publish",
            __("Save options", "modularity"),
            function () {
                $templatePath = \Modularity\Helper\Wp::getTemplate(
                    "publish",
                    "options/partials"
                );
                include $templatePath;
            },
            $this->screenHook,
            "side"
        );

        // Core options
        add_meta_box(
            "modularity-mb-core-options",
            __("Core options", "modularity"),
            function () {
                $templatePath = \Modularity\Helper\Wp::getTemplate(
                    "core-options",
                    "options/partials"
                );
                include $templatePath;
            },
            $this->screenHook,
            "normal"
        );

        if (has_action("Modularity/Options/Module")) {
            add_meta_box(
                "modularity-mb-module-options",
                __("Module options", "modularity"),
                function () {
                    do_action("Modularity/Options/Module");
                },
                $this->screenHook,
                "normal"
            );
        }

        // Modules
        add_meta_box(
            "modularity-mb-post-types",
            __("Post types", "modularity"),
            [$this, "metaBoxPostTypes"],
            $this->screenHook,
            "normal"
        );

        // Templates and areas
        add_meta_box(
            "modularity-mb-template-areas",
            __("Template areas", "modularity"),
            [$this, "metaBoxTemplateAreas"],
            $this->screenHook,
            "normal"
        );

        // Modules
        add_meta_box(
            "modularity-mb-modules",
            __("Modules", "modularity"),
            [$this, "metaBoxModules"],
            $this->screenHook,
            "normal"
        );
    }

    /**
     * Template areas meta box
     * @return void
     */
    public function metaBoxTemplateAreas()
    {
        global $wp_registered_sidebars;
        global $modularityOptions;

        usort($wp_registered_sidebars, function ($a, $b) {
            return $a["name"] > $b["name"];
        });

        $wp_registered_sidebars = apply_filters(
            "Modularity/Templates/Sidebars",
            $wp_registered_sidebars
        );

        $coreTemplates = \Modularity\Helper\Wp::getCoreTemplates();
        $coreTemplates = apply_filters(
            "Modularity/CoreTemplatesInTheme",
            $coreTemplates
        );
        $customTemplates = get_page_templates();
        $templates = array_merge($coreTemplates, $customTemplates);

        include MODULARITY_TEMPLATE_PATH .
            "options/partials/modularity-template-areas.php";
    }

    /**
     * Metabox content: Post types
     * @return void
     */
    public function metaBoxPostTypes()
    {
        global $modularityOptions;
        $enabled =
            isset($modularityOptions["enabled-post-types"]) &&
            is_array($modularityOptions["enabled-post-types"])
                ? $modularityOptions["enabled-post-types"]
                : [];

        $postTypes = array_filter(get_post_types(), function ($item) {
            $disallowed = array_merge(
                array_keys(\Modularity\ModuleManager::$available),
                [
                    "attachment",
                    "revision",
                    "nav_menu_item",
                    "custom_css",
                    "customize_changeset",
                ]
            );

            if (in_array($item, $disallowed)) {
                return false;
            }

            if (substr($item, 0, 4) == "acf-") {
                return false;
            }

            return true;
        });

        include MODULARITY_TEMPLATE_PATH .
            "options/partials/modularity-post-types.php";
    }

    /**
     * Metabox content: Modules
     * @return void
     */
    public function metaBoxModules()
    {
        $available = \Modularity\ModuleManager::$available;

        uasort($available, function ($a, $b) {
            return strcmp($a["labels"]["name"], $b["labels"]["name"]);
        });

        global $modularityOptions;
        $enabled =
            isset($modularityOptions["enabled-modules"]) &&
            is_array($modularityOptions["enabled-modules"])
                ? $modularityOptions["enabled-modules"]
                : [];

        $templatePath = \Modularity\Helper\Wp::getTemplate(
            "modules",
            "options/partials"
        );
        include $templatePath;
    }
}

# Hooks Registrar

### Description

The Hooks Registrar provides a standardized way to register WordPress hooks (actions and filters) within the Municipio theme. It promotes a cleaner and more organized approach to managing hooks, making it easier to understand and maintain the theme's functionality.  It utilizes the `Hookable` interface to define objects that can register hooks and the `HooksRegistrarInterface` to define how to register those objects. The `HooksRegistrar` class itself implements the registration logic. This separation of concerns allows for easier testing and maintainability.

## How to Utilize

### For Developers

1.  **Implement the `Hookable` interface:** Create a class that implements the `Municipio\HooksRegistrar\Hookable` interface. This class will contain the logic for adding your WordPress hooks.

    ```php
    <?php

    namespace MyPlugin;

    use Municipio\HooksRegistrar\Hookable;

    class MyHookableClass implements Hookable
    {
        public function addHooks(): void
        {
            add_action('init', [$this, 'myInitFunction']);
            add_filter('the_content', [$this, 'myContentFilter']);
        }

        public function myInitFunction() {
            // Your initialization logic here
        }

        public function myContentFilter($content) {
            // Modify the content here
            return $content;
        }
    }
    ```

2.  **Register the Hookable object:** Use the `HooksRegistrar` to register your `Hookable` object.  Typically, this would be done in a central location within your theme or plugin, such as a setup or initialization function.

    ```php
    <?php
    use Municipio\HooksRegistrar\HooksRegistrar;
    use MyPlugin\MyHookableClass;

    $hooksRegistrar = new HooksRegistrar();
    $myHookable = new MyHookableClass();
    $hooksRegistrar->register($myHookable);
    ```

### For Administrators / Editors

Administrators and editors do not directly interact with the Hooks Registrar.  The functionality provided by the registered hooks will be available to them depending on what the hooks themselves do.  For example, if a hook registers a new shortcode, editors can then use that shortcode in their content.  If a hook modifies the display of a particular post type, administrators will see that change reflected in the admin area. No specific capabilities are required to benefit from hooks registered in this way.

### For Users

Users will experience the effects of the registered hooks on the frontend of the website. For example, if a hook modifies the output of a specific template, users will see the modified output when they view the relevant page.

---

## Purpose

### Why This Feature Exists

The Hooks Registrar exists to address the problem of scattered and unorganized hook registrations in WordPress themes and plugins.  By providing a centralized and structured approach, it improves code readability, maintainability, and testability.  This makes it easier for developers to understand how hooks are used within the theme and reduces the risk of conflicts or unintended side effects.

### Key Benefits

✅ **Centralizes functionality or information:** All hook registrations are managed through the `HooksRegistrar`, providing a single point of reference.
✅ **Improves the user experience by:** ensuring consistent and predictable behavior of WordPress hooks.  It also makes it easier to debug issues related to hooks.
✅ **Reduces manual work for:** developers by providing a simple and reusable mechanism for registering hooks.  This allows developers to focus on the logic of their hooks rather than the mechanics of registering them.

---

## Meta

- **Author:** [Your Name or Team]
- **Initial Release Date:** [October, 2023]
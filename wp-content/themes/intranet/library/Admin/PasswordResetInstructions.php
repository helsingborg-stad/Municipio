<?php

namespace Intranet\Admin;

class PasswordResetInstructions
{
    public function __construct()
    {
        add_action('network_admin_menu', array($this, 'addOptionsPage'));
        add_action('admin_init', array($this, 'savePasswordInstructions'));
    }

    public function addOptionsPage()
    {
        add_menu_page(
            __('Password', 'municipio-intranet'),
            __('Password', 'municipio-intranet'),
            'manage_network',
            'password',
            function () {
                include INTRANET_PATH . 'templates/admin/password/form.php';
            },
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNS44MDQgMTUuODA0Ij48cGF0aCBkPSJNMTIuNjQgNi45MVY0Ljc4NEMxMi42NCAyLjE0NyAxMC40OTIgMCA3Ljg1MyAwUzMuMDcgMi4xNDcgMy4wNyA0Ljc4NVY2LjkxaC0uOTN2OC44OTRoMTEuNTIyVjYuOTFIMTIuNjR6TTguOTggMTEuNTF2MS44OGEuODg3Ljg4NyAwIDAgMS0xLjc3NCAwdi0xLjg4YTEuMzkgMS4zOSAwIDAgMS0uNTEtMS4wNyAxLjM5NyAxLjM5NyAwIDAgMSAyLjc5NCAwYzAgLjQzMi0uMi44MTQtLjUxIDEuMDd6TTEwLjkgNi45MUg0LjgxVjQuNzg0QTMuMDQ4IDMuMDQ4IDAgMCAxIDcuODU3IDEuNzQgMy4wNDggMy4wNDggMCAwIDEgMTAuOSA0Ljc4NVY2LjkxeiIgZmlsbD0iIzAzMDEwNCIvPjwvc3ZnPg==',
            100
        );
    }

    public function savePasswordInstructions()
    {
        if (!isset($_POST['password-reset-action']) || (!isset($_POST['password-instructions']) || empty($_POST['password-instructions']))) {
            return;
        }

        update_site_option('password-reset-instructions', $_POST['password-instructions']);
    }
}

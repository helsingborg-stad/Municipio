interface ModularityBlockEditor {
    editModulesLinkLabel: string;
    editModulesLinkHref: string;
}

const { PluginSidebar } = wp.editor;
const { registerPlugin } = wp.plugins;
const { Fragment } = wp.element;
const { PanelBody, Button } = wp.components;

declare const modularityBlockEditor: ModularityBlockEditor;

interface WP {
    editor: {
        PluginSidebar: any;
    };
    plugins: {
        registerPlugin: any;
    };
    element: any;
    components: any;
    data: {
        subscribe(callback: () => void): void;
    };
}

declare const wp: WP;

function ModulesPluginSidebar() {
    const { editModulesLinkLabel, editModulesLinkHref } = modularityBlockEditor;

    const sidebar = wp.element.createElement(
        wp.editor.PluginSidebar,
        {
            name: 'modules-plugins-sidebar',
            icon: 'grid-view',
            title: editModulesLinkLabel,
        },
        wp.element.createElement(
            wp.components.PanelBody,
            null,
            wp.element.createElement(
                wp.components.Button,
                {
                    isPrimary: true,
                    onClick: function () {
                        window.location.href = editModulesLinkHref;
                    }
                },
                editModulesLinkLabel
            )
        )
    );

    return wp.element.createElement(
        wp.element.Fragment,
        null,
        sidebar
    );
}

// Register the plugin
wp.plugins.registerPlugin('modules-plugins-sidebar', {
    render: ModulesPluginSidebar
});
(function () {
    if (typeof tinymce !== 'undefined') {
        let inlineStyles = document.querySelector('#municipio_customizer_inline_styles');
        const localizedConfig = typeof mce_hbg_buttons === 'object' ? mce_hbg_buttons : {};
        const themeUrl = localizedConfig.themeUrl ?? '';
        const styleSheet = localizedConfig.styleSheet ?? '';
        let currentButtonState = {
            buttonClass: 'c-button c-button__filled c-button__filled--default c-button--md ripple ripple--before',
            buttonText: 'Button text',
            buttonLink: '#',
            openInNewWindow: false,
        };

        const buildTemplateUrl = function () {
            const fallbackTemplatePath =
                '/wp-content/themes/municipio/library/Admin/TinyMce/MceButtons/mce-buttons-template.php';

            if (!themeUrl) {
                return fallbackTemplatePath;
            }

            try {
                const parsedThemeUrl = new URL(themeUrl, window.location.href);
                const themePath = parsedThemeUrl.pathname.replace(/\/$/, '');

                return `${themePath}/library/Admin/TinyMce/MceButtons/mce-buttons-template.php`;
            } catch (error) {
                return fallbackTemplatePath;
            }
        };

        const templateUrl = buildTemplateUrl();

        window.addEventListener('message', function (event) {
            if (!event || !event.data || event.data.type !== 'municipio:mceButtonState') {
                return;
            }

            const payload = event.data.payload ?? {};

            currentButtonState = {
                buttonClass:
                    payload.buttonClass ||
                    'c-button c-button__filled c-button__filled--default c-button--md ripple ripple--before',
                buttonText: payload.buttonText || 'Button text',
                buttonLink: payload.buttonLink || '#',
                openInNewWindow: Boolean(payload.openInNewWindow),
            };
        });

        if (inlineStyles) {
            inlineStyles = inlineStyles.innerHTML;
        }

        tinymce.PluginManager.add('mce_hbg_buttons', function (editor, url) {
            editor.addButton('mce_hbg_buttons', {
                text: 'Button',
                icon: '',
                context: 'insert',
                tooltip: 'Add button',
                cmd: 'mce_hbg_buttons',
            });

            editor.addCommand('mce_hbg_buttons', function () {
                currentButtonState = {
                    buttonClass: 'c-button c-button__filled c-button__filled--default c-button--md ripple ripple--before',
                    buttonText: 'Button text',
                    buttonLink: '#',
                    openInNewWindow: false,
                };

                const templateParams = new URLSearchParams({
                    styleguideUrl: styleSheet,
                    inlineStyles: inlineStyles ?? '',
                });

                editor.windowManager.open(
                    {
                        title: 'Add button',
                        url: `${templateUrl}?${templateParams.toString()}`,
                        width: 500,
                        height: 420,
                        buttons: [
                            {
                                text: 'Insert',
                                onclick: function (e) {
                                    const btnClass = currentButtonState.buttonClass;
                                    const btnText = currentButtonState.buttonText;
                                    const btnLink = currentButtonState.buttonLink;
                                    const openInNewWindow = currentButtonState.openInNewWindow;

                                    const button = `
                                        <a href="${btnLink}"${openInNewWindow ? ' target="_blank" rel="noopener"' : ''} class="${btnClass} u-no-decoration">
                                            <span class="c-button__label">
                                                <span class="c-button__label-text">
                                                    ${btnText}
                                                </span>
                                            </span>
                                        </a>
                                    `;

                                    editor.insertContent(button);
                                    editor.windowManager.close();
                                    return true;
                                },
                            },
                        ],
                    },
                    {
                        stylesSheet: {styleguideUrl: styleSheet, inlineStyles: inlineStyles ?? ''},
                    },
                );
            });
        });
    }
})();

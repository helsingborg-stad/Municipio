(function() {
    if (typeof tinymce !== 'undefined') {
        tinymce.PluginManager.add('mce_hbg_buttons', function(editor, url) {
        editor.addButton('mce_hbg_buttons', {
            text: 'Button',
            icon: '',
            context: 'insert',
            tooltip: 'Add button',
            cmd: 'mce_hbg_buttons'
        });

        editor.addCommand('mce_hbg_buttons', function() {
            editor.windowManager.open({
                title: 'Add button',
                url: mce_hbg_buttons.themeUrl + '/library/Admin/TinyMce/MceButtons/mce-buttons-template.php',
                width: 500,
                height: 420,
                buttons: [
                    {
                        text: 'Insert',
                        onclick: function(e) {
                            var $iframe = jQuery('.mce-container-body.mce-window-body.mce-abs-layout iframe').contents();
                            var btnClass = $iframe.find('#preview a').attr('class');
                            var btnText = $iframe.find('#btnText').val();
                            var btnLink = $iframe.find('#btnLink').val();
                            var button = '<a href="' + btnLink + '" class="' + btnClass + '">'+ btnText +'</a>';
                            editor.insertContent(button);
                            editor.windowManager.close();
                            return true;
                        }
                    }
                ]
            },
            {
                stylesSheet: mce_hbg_buttons.styleSheet
            }
            );
        });
    });
    }
})();

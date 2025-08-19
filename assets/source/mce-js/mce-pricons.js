(function() {
    tinymce.PluginManager.add('pricons', function(editor, url) {
        editor.addButton('pricons', {
            text: '',
            icon: 'pricon-smiley-cool',
            context: 'insert',
            tooltip: 'Pricon',
            cmd: 'openInsertPiconModal'
        });


        editor.addCommand('openInsertPiconModal', function() {
            editor.windowManager.open({
                title: 'Pricons',
                url: url + '/mce-picons.php',
                width: 500,
                height: 400,
                buttons: [
                    {
                        text: 'Insert',
                        onclick: function(e) {
                            var $iframe = jQuery('.mce-container-body.mce-window-body.mce-abs-layout iframe').contents();
                            var size = $iframe.find('[name="pricon-size"]').val();
                            var color = $iframe.find('[name="pricon-color"]').val();
                            var icon = $iframe.find('[name="pricon-icon"]').val();

                            if (!icon.length) {
                                editor.windowManager.close();
                                return false;
                            }

                            var shortcode = '[pricon icon="' + icon + '"';

                            if (color.length) {
                                shortcode = shortcode + ' color="' + color + '"';
                            }

                            if (size.length) {
                                shortcode = shortcode + ' size="' + size + '"';
                            }

                            shortcode = shortcode + ']';

                            editor.insertContent(shortcode);

                            editor.windowManager.close();
                            return true;
                        }
                    }
                ]
            });

        });
    });
})();

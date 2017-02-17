(function() {
    tinymce.PluginManager.add('pricons', function(editor, url) {
        editor.addButton('pricons', {
            text: '',
            icon: 'pricon-smiley-cool',
            context: 'insert',
            tooltip: 'Pricon',
            onclick: function(e) {

            }
        });
    });
})();

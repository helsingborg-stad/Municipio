(function() {
    tinymce.PluginManager.add('metadata', function(editor, url) {
        editor.addButton( 'metadata', {
            type: 'listbox',
            text: 'Metadata',
            icon: false,
            onselect: function(e) {
                editor.insertContent(this.value());
                this.value('');
            },
            values: metadata_button
        });
    });
})();

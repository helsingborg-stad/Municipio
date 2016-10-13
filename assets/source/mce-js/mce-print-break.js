(function() {
    tinymce.PluginManager.add('print_break', function(editor, url) {
        editor.addButton('printbreak', {
            text: '',
            icon: 'wp_page',
            context: 'insert',
            tooltip: 'Insert Print Page Break tag',
            onclick: function(e) {
                editor.execCommand('Print_Break');
            }
        });

        editor.addCommand('Print_Break', function() {
            var parent;
            var html;

            var tag = 'printbreak';
            var title = 'Print Break';
            var classname = 'wp-print-break-tag mce-wp-' + tag;
            var dom = editor.dom;
            var node = editor.selection.getNode();

            html = '<img src="' + tinymce.Env.transparentSrc + '" alt="" title="' + title + '" class="' + classname + '" ' +
                'data-mce-resize="false" data-mce-placeholder="1" data-wp-more="printbreak" />';

            // Most common case
            if (node.nodeName === 'BODY' || (node.nodeName === 'P' && node.parentNode.nodeName === 'BODY')) {
                editor.insertContent(html);
                return;
            }

            // Get the top level parent node
            parent = dom.getParent(node, function(found) {
                if (found.parentNode && found.parentNode.nodeName === 'BODY') {
                    return true;
                }

                return false;
            }, editor.getBody());

            if (parent) {
                if (parent.nodeName === 'P') {
                    parent.appendChild(dom.create('p', null, html).firstChild);
                } else {
                    dom.insertAfter( dom.create('p', null, html), parent);
                }

                editor.nodeChanged();
            }
        });

        editor.on( 'BeforeSetContent', function( event ) {
            var title;

            if ( event.content ) {
                if ( event.content.indexOf( '<!--printbreak-->' ) !== -1 ) {
                    title = 'Print Break';

                    event.content = event.content.replace( /<!--printbreak-->/g,
                        '<img src="' + tinymce.Env.transparentSrc + '" class="wp-print-break-tag mce-wp-printbreak" ' +
                            'alt="" title="' + title + '" data-wp-more="printbreak" data-mce-resize="false" data-mce-placeholder="1" />' );
                }
            }
        });

        editor.on( 'PostProcess', function( event ) {
            if ( event.get ) {
                event.content = event.content.replace(/<img[^>]+>/g, function( image ) {
                    var match,
                        string,
                        moretext = '';

                    if ( image.indexOf('data-wp-more="printbreak"') !== -1 ) {
                        string = '<!--printbreak-->';
                    }

                    return string || image;
                });
            }
        });
    });
})();

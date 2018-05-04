(function() {
    console.log(mce_hbg_buttons);
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

                            var button = '<a href="' + btnLink + '" class="' + btnClass + '">'+ btnText +'</a>'

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

    };


})();

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

(function() {
    tinymce.PluginManager.add('print_break', function(editor, url) {
        editor.addButton('printbreak', {
            text: '',
            icon: 'wp_page',
            context: 'insert',
            tooltip: 'Print Break',
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

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1jZS1idXR0b25zLmpzIiwibWNlLW1ldGFkYXRhLmpzIiwibWNlLXByaWNvbnMuanMiLCJtY2UtcHJpbnQtYnJlYWsuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNoREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDZEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3ZEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6Im1jZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbigpIHtcbiAgICBjb25zb2xlLmxvZyhtY2VfaGJnX2J1dHRvbnMpO1xuICAgIGlmICh0eXBlb2YgdGlueW1jZSAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgdGlueW1jZS5QbHVnaW5NYW5hZ2VyLmFkZCgnbWNlX2hiZ19idXR0b25zJywgZnVuY3Rpb24oZWRpdG9yLCB1cmwpIHtcbiAgICAgICAgZWRpdG9yLmFkZEJ1dHRvbignbWNlX2hiZ19idXR0b25zJywge1xuICAgICAgICAgICAgdGV4dDogJ0J1dHRvbicsXG4gICAgICAgICAgICBpY29uOiAnJyxcbiAgICAgICAgICAgIGNvbnRleHQ6ICdpbnNlcnQnLFxuICAgICAgICAgICAgdG9vbHRpcDogJ0FkZCBidXR0b24nLFxuICAgICAgICAgICAgY21kOiAnbWNlX2hiZ19idXR0b25zJ1xuICAgICAgICB9KTtcblxuICAgICAgICBlZGl0b3IuYWRkQ29tbWFuZCgnbWNlX2hiZ19idXR0b25zJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBlZGl0b3Iud2luZG93TWFuYWdlci5vcGVuKHtcbiAgICAgICAgICAgICAgICB0aXRsZTogJ0FkZCBidXR0b24nLFxuICAgICAgICAgICAgICAgIHVybDogbWNlX2hiZ19idXR0b25zLnRoZW1lVXJsICsgJy9saWJyYXJ5L0FkbWluL1RpbnlNY2UvTWNlQnV0dG9ucy9tY2UtYnV0dG9ucy10ZW1wbGF0ZS5waHAnLFxuICAgICAgICAgICAgICAgIHdpZHRoOiA1MDAsXG4gICAgICAgICAgICAgICAgaGVpZ2h0OiA0MjAsXG4gICAgICAgICAgICAgICAgYnV0dG9uczogW1xuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0ZXh0OiAnSW5zZXJ0JyxcbiAgICAgICAgICAgICAgICAgICAgICAgIG9uY2xpY2s6IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGlmcmFtZSA9IGpRdWVyeSgnLm1jZS1jb250YWluZXItYm9keS5tY2Utd2luZG93LWJvZHkubWNlLWFicy1sYXlvdXQgaWZyYW1lJykuY29udGVudHMoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgYnRuQ2xhc3MgPSAkaWZyYW1lLmZpbmQoJyNwcmV2aWV3IGEnKS5hdHRyKCdjbGFzcycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBidG5UZXh0ID0gJGlmcmFtZS5maW5kKCcjYnRuVGV4dCcpLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBidG5MaW5rID0gJGlmcmFtZS5maW5kKCcjYnRuTGluaycpLnZhbCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGJ1dHRvbiA9ICc8YSBocmVmPVwiJyArIGJ0bkxpbmsgKyAnXCIgY2xhc3M9XCInICsgYnRuQ2xhc3MgKyAnXCI+JysgYnRuVGV4dCArJzwvYT4nXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlZGl0b3IuaW5zZXJ0Q29udGVudChidXR0b24pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWRpdG9yLndpbmRvd01hbmFnZXIuY2xvc2UoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIF1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc3R5bGVzU2hlZXQ6IG1jZV9oYmdfYnV0dG9ucy5zdHlsZVNoZWV0XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICApO1xuICAgICAgICB9KTtcbiAgICAgICAgfSk7XG5cbiAgICB9O1xuXG5cbn0pKCk7XG4iLCIoZnVuY3Rpb24oKSB7XG4gICAgdGlueW1jZS5QbHVnaW5NYW5hZ2VyLmFkZCgnbWV0YWRhdGEnLCBmdW5jdGlvbihlZGl0b3IsIHVybCkge1xuICAgICAgICBlZGl0b3IuYWRkQnV0dG9uKCAnbWV0YWRhdGEnLCB7XG4gICAgICAgICAgICB0eXBlOiAnbGlzdGJveCcsXG4gICAgICAgICAgICB0ZXh0OiAnTWV0YWRhdGEnLFxuICAgICAgICAgICAgaWNvbjogZmFsc2UsXG4gICAgICAgICAgICBvbnNlbGVjdDogZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIGVkaXRvci5pbnNlcnRDb250ZW50KHRoaXMudmFsdWUoKSk7XG4gICAgICAgICAgICAgICAgdGhpcy52YWx1ZSgnJyk7XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgdmFsdWVzOiBtZXRhZGF0YV9idXR0b25cbiAgICAgICAgfSk7XG4gICAgfSk7XG59KSgpO1xuIiwiKGZ1bmN0aW9uKCkge1xuICAgIHRpbnltY2UuUGx1Z2luTWFuYWdlci5hZGQoJ3ByaWNvbnMnLCBmdW5jdGlvbihlZGl0b3IsIHVybCkge1xuICAgICAgICBlZGl0b3IuYWRkQnV0dG9uKCdwcmljb25zJywge1xuICAgICAgICAgICAgdGV4dDogJycsXG4gICAgICAgICAgICBpY29uOiAncHJpY29uLXNtaWxleS1jb29sJyxcbiAgICAgICAgICAgIGNvbnRleHQ6ICdpbnNlcnQnLFxuICAgICAgICAgICAgdG9vbHRpcDogJ1ByaWNvbicsXG4gICAgICAgICAgICBjbWQ6ICdvcGVuSW5zZXJ0UGljb25Nb2RhbCdcbiAgICAgICAgfSk7XG5cblxuICAgICAgICBlZGl0b3IuYWRkQ29tbWFuZCgnb3Blbkluc2VydFBpY29uTW9kYWwnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGVkaXRvci53aW5kb3dNYW5hZ2VyLm9wZW4oe1xuICAgICAgICAgICAgICAgIHRpdGxlOiAnUHJpY29ucycsXG4gICAgICAgICAgICAgICAgdXJsOiB1cmwgKyAnL21jZS1waWNvbnMucGhwJyxcbiAgICAgICAgICAgICAgICB3aWR0aDogNTAwLFxuICAgICAgICAgICAgICAgIGhlaWdodDogNDAwLFxuICAgICAgICAgICAgICAgIGJ1dHRvbnM6IFtcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGV4dDogJ0luc2VydCcsXG4gICAgICAgICAgICAgICAgICAgICAgICBvbmNsaWNrOiBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRpZnJhbWUgPSBqUXVlcnkoJy5tY2UtY29udGFpbmVyLWJvZHkubWNlLXdpbmRvdy1ib2R5Lm1jZS1hYnMtbGF5b3V0IGlmcmFtZScpLmNvbnRlbnRzKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHNpemUgPSAkaWZyYW1lLmZpbmQoJ1tuYW1lPVwicHJpY29uLXNpemVcIl0nKS52YWwoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgY29sb3IgPSAkaWZyYW1lLmZpbmQoJ1tuYW1lPVwicHJpY29uLWNvbG9yXCJdJykudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGljb24gPSAkaWZyYW1lLmZpbmQoJ1tuYW1lPVwicHJpY29uLWljb25cIl0nKS52YWwoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICghaWNvbi5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZWRpdG9yLndpbmRvd01hbmFnZXIuY2xvc2UoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBzaG9ydGNvZGUgPSAnW3ByaWNvbiBpY29uPVwiJyArIGljb24gKyAnXCInO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbG9yLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG9ydGNvZGUgPSBzaG9ydGNvZGUgKyAnIGNvbG9yPVwiJyArIGNvbG9yICsgJ1wiJztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoc2l6ZS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvcnRjb2RlID0gc2hvcnRjb2RlICsgJyBzaXplPVwiJyArIHNpemUgKyAnXCInO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3J0Y29kZSA9IHNob3J0Y29kZSArICddJztcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVkaXRvci5pbnNlcnRDb250ZW50KHNob3J0Y29kZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlZGl0b3Iud2luZG93TWFuYWdlci5jbG9zZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgXVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSk7XG4gICAgfSk7XG59KSgpO1xuIiwiKGZ1bmN0aW9uKCkge1xuICAgIHRpbnltY2UuUGx1Z2luTWFuYWdlci5hZGQoJ3ByaW50X2JyZWFrJywgZnVuY3Rpb24oZWRpdG9yLCB1cmwpIHtcbiAgICAgICAgZWRpdG9yLmFkZEJ1dHRvbigncHJpbnRicmVhaycsIHtcbiAgICAgICAgICAgIHRleHQ6ICcnLFxuICAgICAgICAgICAgaWNvbjogJ3dwX3BhZ2UnLFxuICAgICAgICAgICAgY29udGV4dDogJ2luc2VydCcsXG4gICAgICAgICAgICB0b29sdGlwOiAnUHJpbnQgQnJlYWsnLFxuICAgICAgICAgICAgb25jbGljazogZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgIGVkaXRvci5leGVjQ29tbWFuZCgnUHJpbnRfQnJlYWsnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgZWRpdG9yLmFkZENvbW1hbmQoJ1ByaW50X0JyZWFrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB2YXIgcGFyZW50O1xuICAgICAgICAgICAgdmFyIGh0bWw7XG5cbiAgICAgICAgICAgIHZhciB0YWcgPSAncHJpbnRicmVhayc7XG4gICAgICAgICAgICB2YXIgdGl0bGUgPSAnUHJpbnQgQnJlYWsnO1xuICAgICAgICAgICAgdmFyIGNsYXNzbmFtZSA9ICd3cC1wcmludC1icmVhay10YWcgbWNlLXdwLScgKyB0YWc7XG4gICAgICAgICAgICB2YXIgZG9tID0gZWRpdG9yLmRvbTtcbiAgICAgICAgICAgIHZhciBub2RlID0gZWRpdG9yLnNlbGVjdGlvbi5nZXROb2RlKCk7XG5cbiAgICAgICAgICAgIGh0bWwgPSAnPGltZyBzcmM9XCInICsgdGlueW1jZS5FbnYudHJhbnNwYXJlbnRTcmMgKyAnXCIgYWx0PVwiXCIgdGl0bGU9XCInICsgdGl0bGUgKyAnXCIgY2xhc3M9XCInICsgY2xhc3NuYW1lICsgJ1wiICcgK1xuICAgICAgICAgICAgICAgICdkYXRhLW1jZS1yZXNpemU9XCJmYWxzZVwiIGRhdGEtbWNlLXBsYWNlaG9sZGVyPVwiMVwiIGRhdGEtd3AtbW9yZT1cInByaW50YnJlYWtcIiAvPic7XG5cbiAgICAgICAgICAgIC8vIE1vc3QgY29tbW9uIGNhc2VcbiAgICAgICAgICAgIGlmIChub2RlLm5vZGVOYW1lID09PSAnQk9EWScgfHwgKG5vZGUubm9kZU5hbWUgPT09ICdQJyAmJiBub2RlLnBhcmVudE5vZGUubm9kZU5hbWUgPT09ICdCT0RZJykpIHtcbiAgICAgICAgICAgICAgICBlZGl0b3IuaW5zZXJ0Q29udGVudChodG1sKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIEdldCB0aGUgdG9wIGxldmVsIHBhcmVudCBub2RlXG4gICAgICAgICAgICBwYXJlbnQgPSBkb20uZ2V0UGFyZW50KG5vZGUsIGZ1bmN0aW9uKGZvdW5kKSB7XG4gICAgICAgICAgICAgICAgaWYgKGZvdW5kLnBhcmVudE5vZGUgJiYgZm91bmQucGFyZW50Tm9kZS5ub2RlTmFtZSA9PT0gJ0JPRFknKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH0sIGVkaXRvci5nZXRCb2R5KCkpO1xuXG4gICAgICAgICAgICBpZiAocGFyZW50KSB7XG4gICAgICAgICAgICAgICAgaWYgKHBhcmVudC5ub2RlTmFtZSA9PT0gJ1AnKSB7XG4gICAgICAgICAgICAgICAgICAgIHBhcmVudC5hcHBlbmRDaGlsZChkb20uY3JlYXRlKCdwJywgbnVsbCwgaHRtbCkuZmlyc3RDaGlsZCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgZG9tLmluc2VydEFmdGVyKCBkb20uY3JlYXRlKCdwJywgbnVsbCwgaHRtbCksIHBhcmVudCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgZWRpdG9yLm5vZGVDaGFuZ2VkKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGVkaXRvci5vbiggJ0JlZm9yZVNldENvbnRlbnQnLCBmdW5jdGlvbiggZXZlbnQgKSB7XG4gICAgICAgICAgICB2YXIgdGl0bGU7XG5cbiAgICAgICAgICAgIGlmICggZXZlbnQuY29udGVudCApIHtcbiAgICAgICAgICAgICAgICBpZiAoIGV2ZW50LmNvbnRlbnQuaW5kZXhPZiggJzwhLS1wcmludGJyZWFrLS0+JyApICE9PSAtMSApIHtcbiAgICAgICAgICAgICAgICAgICAgdGl0bGUgPSAnUHJpbnQgQnJlYWsnO1xuXG4gICAgICAgICAgICAgICAgICAgIGV2ZW50LmNvbnRlbnQgPSBldmVudC5jb250ZW50LnJlcGxhY2UoIC88IS0tcHJpbnRicmVhay0tPi9nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzxpbWcgc3JjPVwiJyArIHRpbnltY2UuRW52LnRyYW5zcGFyZW50U3JjICsgJ1wiIGNsYXNzPVwid3AtcHJpbnQtYnJlYWstdGFnIG1jZS13cC1wcmludGJyZWFrXCIgJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2FsdD1cIlwiIHRpdGxlPVwiJyArIHRpdGxlICsgJ1wiIGRhdGEtd3AtbW9yZT1cInByaW50YnJlYWtcIiBkYXRhLW1jZS1yZXNpemU9XCJmYWxzZVwiIGRhdGEtbWNlLXBsYWNlaG9sZGVyPVwiMVwiIC8+JyApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgZWRpdG9yLm9uKCAnUG9zdFByb2Nlc3MnLCBmdW5jdGlvbiggZXZlbnQgKSB7XG4gICAgICAgICAgICBpZiAoIGV2ZW50LmdldCApIHtcbiAgICAgICAgICAgICAgICBldmVudC5jb250ZW50ID0gZXZlbnQuY29udGVudC5yZXBsYWNlKC88aW1nW14+XSs+L2csIGZ1bmN0aW9uKCBpbWFnZSApIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1hdGNoLFxuICAgICAgICAgICAgICAgICAgICAgICAgc3RyaW5nLFxuICAgICAgICAgICAgICAgICAgICAgICAgbW9yZXRleHQgPSAnJztcblxuICAgICAgICAgICAgICAgICAgICBpZiAoIGltYWdlLmluZGV4T2YoJ2RhdGEtd3AtbW9yZT1cInByaW50YnJlYWtcIicpICE9PSAtMSApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0cmluZyA9ICc8IS0tcHJpbnRicmVhay0tPic7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gc3RyaW5nIHx8IGltYWdlO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9KTtcbn0pKCk7XG4iXX0=

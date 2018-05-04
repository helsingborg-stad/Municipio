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

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1jZS1idXR0b25zLmpzIiwibWNlLW1ldGFkYXRhLmpzIiwibWNlLXByaWNvbnMuanMiLCJtY2UtcHJpbnQtYnJlYWsuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDekNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUN2REE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJtY2UuanMiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24oKSB7XG4gICAgaWYgKHR5cGVvZiB0aW55bWNlICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICB0aW55bWNlLlBsdWdpbk1hbmFnZXIuYWRkKCdtY2VfaGJnX2J1dHRvbnMnLCBmdW5jdGlvbihlZGl0b3IsIHVybCkge1xuICAgICAgICBlZGl0b3IuYWRkQnV0dG9uKCdtY2VfaGJnX2J1dHRvbnMnLCB7XG4gICAgICAgICAgICB0ZXh0OiAnQnV0dG9uJyxcbiAgICAgICAgICAgIGljb246ICcnLFxuICAgICAgICAgICAgY29udGV4dDogJ2luc2VydCcsXG4gICAgICAgICAgICB0b29sdGlwOiAnQWRkIGJ1dHRvbicsXG4gICAgICAgICAgICBjbWQ6ICdtY2VfaGJnX2J1dHRvbnMnXG4gICAgICAgIH0pO1xuXG4gICAgICAgIGVkaXRvci5hZGRDb21tYW5kKCdtY2VfaGJnX2J1dHRvbnMnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGVkaXRvci53aW5kb3dNYW5hZ2VyLm9wZW4oe1xuICAgICAgICAgICAgICAgIHRpdGxlOiAnQWRkIGJ1dHRvbicsXG4gICAgICAgICAgICAgICAgdXJsOiBtY2VfaGJnX2J1dHRvbnMudGhlbWVVcmwgKyAnL2xpYnJhcnkvQWRtaW4vVGlueU1jZS9NY2VCdXR0b25zL21jZS1idXR0b25zLXRlbXBsYXRlLnBocCcsXG4gICAgICAgICAgICAgICAgd2lkdGg6IDUwMCxcbiAgICAgICAgICAgICAgICBoZWlnaHQ6IDQyMCxcbiAgICAgICAgICAgICAgICBidXR0b25zOiBbXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRleHQ6ICdJbnNlcnQnLFxuICAgICAgICAgICAgICAgICAgICAgICAgb25jbGljazogZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkaWZyYW1lID0galF1ZXJ5KCcubWNlLWNvbnRhaW5lci1ib2R5Lm1jZS13aW5kb3ctYm9keS5tY2UtYWJzLWxheW91dCBpZnJhbWUnKS5jb250ZW50cygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBidG5DbGFzcyA9ICRpZnJhbWUuZmluZCgnI3ByZXZpZXcgYScpLmF0dHIoJ2NsYXNzJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGJ0blRleHQgPSAkaWZyYW1lLmZpbmQoJyNidG5UZXh0JykudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGJ0bkxpbmsgPSAkaWZyYW1lLmZpbmQoJyNidG5MaW5rJykudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGJ1dHRvbiA9ICc8YSBocmVmPVwiJyArIGJ0bkxpbmsgKyAnXCIgY2xhc3M9XCInICsgYnRuQ2xhc3MgKyAnXCI+JysgYnRuVGV4dCArJzwvYT4nO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVkaXRvci5pbnNlcnRDb250ZW50KGJ1dHRvbik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWRpdG9yLndpbmRvd01hbmFnZXIuY2xvc2UoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIF1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc3R5bGVzU2hlZXQ6IG1jZV9oYmdfYnV0dG9ucy5zdHlsZVNoZWV0XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICApO1xuICAgICAgICB9KTtcbiAgICB9KTtcbiAgICB9XG59KSgpO1xuIiwiKGZ1bmN0aW9uKCkge1xuICAgIHRpbnltY2UuUGx1Z2luTWFuYWdlci5hZGQoJ21ldGFkYXRhJywgZnVuY3Rpb24oZWRpdG9yLCB1cmwpIHtcbiAgICAgICAgZWRpdG9yLmFkZEJ1dHRvbiggJ21ldGFkYXRhJywge1xuICAgICAgICAgICAgdHlwZTogJ2xpc3Rib3gnLFxuICAgICAgICAgICAgdGV4dDogJ01ldGFkYXRhJyxcbiAgICAgICAgICAgIGljb246IGZhbHNlLFxuICAgICAgICAgICAgb25zZWxlY3Q6IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICBlZGl0b3IuaW5zZXJ0Q29udGVudCh0aGlzLnZhbHVlKCkpO1xuICAgICAgICAgICAgICAgIHRoaXMudmFsdWUoJycpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHZhbHVlczogbWV0YWRhdGFfYnV0dG9uXG4gICAgICAgIH0pO1xuICAgIH0pO1xufSkoKTtcbiIsIihmdW5jdGlvbigpIHtcbiAgICB0aW55bWNlLlBsdWdpbk1hbmFnZXIuYWRkKCdwcmljb25zJywgZnVuY3Rpb24oZWRpdG9yLCB1cmwpIHtcbiAgICAgICAgZWRpdG9yLmFkZEJ1dHRvbigncHJpY29ucycsIHtcbiAgICAgICAgICAgIHRleHQ6ICcnLFxuICAgICAgICAgICAgaWNvbjogJ3ByaWNvbi1zbWlsZXktY29vbCcsXG4gICAgICAgICAgICBjb250ZXh0OiAnaW5zZXJ0JyxcbiAgICAgICAgICAgIHRvb2x0aXA6ICdQcmljb24nLFxuICAgICAgICAgICAgY21kOiAnb3Blbkluc2VydFBpY29uTW9kYWwnXG4gICAgICAgIH0pO1xuXG5cbiAgICAgICAgZWRpdG9yLmFkZENvbW1hbmQoJ29wZW5JbnNlcnRQaWNvbk1vZGFsJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBlZGl0b3Iud2luZG93TWFuYWdlci5vcGVuKHtcbiAgICAgICAgICAgICAgICB0aXRsZTogJ1ByaWNvbnMnLFxuICAgICAgICAgICAgICAgIHVybDogdXJsICsgJy9tY2UtcGljb25zLnBocCcsXG4gICAgICAgICAgICAgICAgd2lkdGg6IDUwMCxcbiAgICAgICAgICAgICAgICBoZWlnaHQ6IDQwMCxcbiAgICAgICAgICAgICAgICBidXR0b25zOiBbXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRleHQ6ICdJbnNlcnQnLFxuICAgICAgICAgICAgICAgICAgICAgICAgb25jbGljazogZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkaWZyYW1lID0galF1ZXJ5KCcubWNlLWNvbnRhaW5lci1ib2R5Lm1jZS13aW5kb3ctYm9keS5tY2UtYWJzLWxheW91dCBpZnJhbWUnKS5jb250ZW50cygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBzaXplID0gJGlmcmFtZS5maW5kKCdbbmFtZT1cInByaWNvbi1zaXplXCJdJykudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGNvbG9yID0gJGlmcmFtZS5maW5kKCdbbmFtZT1cInByaWNvbi1jb2xvclwiXScpLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBpY29uID0gJGlmcmFtZS5maW5kKCdbbmFtZT1cInByaWNvbi1pY29uXCJdJykudmFsKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoIWljb24ubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVkaXRvci53aW5kb3dNYW5hZ2VyLmNsb3NlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgc2hvcnRjb2RlID0gJ1twcmljb24gaWNvbj1cIicgKyBpY29uICsgJ1wiJztcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb2xvci5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvcnRjb2RlID0gc2hvcnRjb2RlICsgJyBjb2xvcj1cIicgKyBjb2xvciArICdcIic7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHNpemUubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3J0Y29kZSA9IHNob3J0Y29kZSArICcgc2l6ZT1cIicgKyBzaXplICsgJ1wiJztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG9ydGNvZGUgPSBzaG9ydGNvZGUgKyAnXSc7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlZGl0b3IuaW5zZXJ0Q29udGVudChzaG9ydGNvZGUpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWRpdG9yLndpbmRvd01hbmFnZXIuY2xvc2UoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIF1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0pO1xuICAgIH0pO1xufSkoKTtcbiIsIihmdW5jdGlvbigpIHtcbiAgICB0aW55bWNlLlBsdWdpbk1hbmFnZXIuYWRkKCdwcmludF9icmVhaycsIGZ1bmN0aW9uKGVkaXRvciwgdXJsKSB7XG4gICAgICAgIGVkaXRvci5hZGRCdXR0b24oJ3ByaW50YnJlYWsnLCB7XG4gICAgICAgICAgICB0ZXh0OiAnJyxcbiAgICAgICAgICAgIGljb246ICd3cF9wYWdlJyxcbiAgICAgICAgICAgIGNvbnRleHQ6ICdpbnNlcnQnLFxuICAgICAgICAgICAgdG9vbHRpcDogJ1ByaW50IEJyZWFrJyxcbiAgICAgICAgICAgIG9uY2xpY2s6IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICBlZGl0b3IuZXhlY0NvbW1hbmQoJ1ByaW50X0JyZWFrJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGVkaXRvci5hZGRDb21tYW5kKCdQcmludF9CcmVhaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdmFyIHBhcmVudDtcbiAgICAgICAgICAgIHZhciBodG1sO1xuXG4gICAgICAgICAgICB2YXIgdGFnID0gJ3ByaW50YnJlYWsnO1xuICAgICAgICAgICAgdmFyIHRpdGxlID0gJ1ByaW50IEJyZWFrJztcbiAgICAgICAgICAgIHZhciBjbGFzc25hbWUgPSAnd3AtcHJpbnQtYnJlYWstdGFnIG1jZS13cC0nICsgdGFnO1xuICAgICAgICAgICAgdmFyIGRvbSA9IGVkaXRvci5kb207XG4gICAgICAgICAgICB2YXIgbm9kZSA9IGVkaXRvci5zZWxlY3Rpb24uZ2V0Tm9kZSgpO1xuXG4gICAgICAgICAgICBodG1sID0gJzxpbWcgc3JjPVwiJyArIHRpbnltY2UuRW52LnRyYW5zcGFyZW50U3JjICsgJ1wiIGFsdD1cIlwiIHRpdGxlPVwiJyArIHRpdGxlICsgJ1wiIGNsYXNzPVwiJyArIGNsYXNzbmFtZSArICdcIiAnICtcbiAgICAgICAgICAgICAgICAnZGF0YS1tY2UtcmVzaXplPVwiZmFsc2VcIiBkYXRhLW1jZS1wbGFjZWhvbGRlcj1cIjFcIiBkYXRhLXdwLW1vcmU9XCJwcmludGJyZWFrXCIgLz4nO1xuXG4gICAgICAgICAgICAvLyBNb3N0IGNvbW1vbiBjYXNlXG4gICAgICAgICAgICBpZiAobm9kZS5ub2RlTmFtZSA9PT0gJ0JPRFknIHx8IChub2RlLm5vZGVOYW1lID09PSAnUCcgJiYgbm9kZS5wYXJlbnROb2RlLm5vZGVOYW1lID09PSAnQk9EWScpKSB7XG4gICAgICAgICAgICAgICAgZWRpdG9yLmluc2VydENvbnRlbnQoaHRtbCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBHZXQgdGhlIHRvcCBsZXZlbCBwYXJlbnQgbm9kZVxuICAgICAgICAgICAgcGFyZW50ID0gZG9tLmdldFBhcmVudChub2RlLCBmdW5jdGlvbihmb3VuZCkge1xuICAgICAgICAgICAgICAgIGlmIChmb3VuZC5wYXJlbnROb2RlICYmIGZvdW5kLnBhcmVudE5vZGUubm9kZU5hbWUgPT09ICdCT0RZJykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9LCBlZGl0b3IuZ2V0Qm9keSgpKTtcblxuICAgICAgICAgICAgaWYgKHBhcmVudCkge1xuICAgICAgICAgICAgICAgIGlmIChwYXJlbnQubm9kZU5hbWUgPT09ICdQJykge1xuICAgICAgICAgICAgICAgICAgICBwYXJlbnQuYXBwZW5kQ2hpbGQoZG9tLmNyZWF0ZSgncCcsIG51bGwsIGh0bWwpLmZpcnN0Q2hpbGQpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGRvbS5pbnNlcnRBZnRlciggZG9tLmNyZWF0ZSgncCcsIG51bGwsIGh0bWwpLCBwYXJlbnQpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGVkaXRvci5ub2RlQ2hhbmdlZCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICBlZGl0b3Iub24oICdCZWZvcmVTZXRDb250ZW50JywgZnVuY3Rpb24oIGV2ZW50ICkge1xuICAgICAgICAgICAgdmFyIHRpdGxlO1xuXG4gICAgICAgICAgICBpZiAoIGV2ZW50LmNvbnRlbnQgKSB7XG4gICAgICAgICAgICAgICAgaWYgKCBldmVudC5jb250ZW50LmluZGV4T2YoICc8IS0tcHJpbnRicmVhay0tPicgKSAhPT0gLTEgKSB7XG4gICAgICAgICAgICAgICAgICAgIHRpdGxlID0gJ1ByaW50IEJyZWFrJztcblxuICAgICAgICAgICAgICAgICAgICBldmVudC5jb250ZW50ID0gZXZlbnQuY29udGVudC5yZXBsYWNlKCAvPCEtLXByaW50YnJlYWstLT4vZyxcbiAgICAgICAgICAgICAgICAgICAgICAgICc8aW1nIHNyYz1cIicgKyB0aW55bWNlLkVudi50cmFuc3BhcmVudFNyYyArICdcIiBjbGFzcz1cIndwLXByaW50LWJyZWFrLXRhZyBtY2Utd3AtcHJpbnRicmVha1wiICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICdhbHQ9XCJcIiB0aXRsZT1cIicgKyB0aXRsZSArICdcIiBkYXRhLXdwLW1vcmU9XCJwcmludGJyZWFrXCIgZGF0YS1tY2UtcmVzaXplPVwiZmFsc2VcIiBkYXRhLW1jZS1wbGFjZWhvbGRlcj1cIjFcIiAvPicgKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGVkaXRvci5vbiggJ1Bvc3RQcm9jZXNzJywgZnVuY3Rpb24oIGV2ZW50ICkge1xuICAgICAgICAgICAgaWYgKCBldmVudC5nZXQgKSB7XG4gICAgICAgICAgICAgICAgZXZlbnQuY29udGVudCA9IGV2ZW50LmNvbnRlbnQucmVwbGFjZSgvPGltZ1tePl0rPi9nLCBmdW5jdGlvbiggaW1hZ2UgKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBtYXRjaCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0cmluZyxcbiAgICAgICAgICAgICAgICAgICAgICAgIG1vcmV0ZXh0ID0gJyc7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCBpbWFnZS5pbmRleE9mKCdkYXRhLXdwLW1vcmU9XCJwcmludGJyZWFrXCInKSAhPT0gLTEgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzdHJpbmcgPSAnPCEtLXByaW50YnJlYWstLT4nO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHN0cmluZyB8fCBpbWFnZTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSk7XG59KSgpO1xuIl19

tinymce.PluginManager.add('table', function(editor) {
    editor.addButton('table', {
        title: 'Table',
        icon: 'table',
        onclick: function() {
            editor.windowManager.open({
                title: 'Insert Table',
                body: [
                    {
                        type: 'textbox',
                        name: 'rows',
                        label: 'Rows',
                        value: '2'
                    },
                    {
                        type: 'textbox',
                        name: 'cols',
                        label: 'Columns',
                        value: '2'
                    }
                ],
                onsubmit: function(e) {
                    var html = buildTable(e.data.rows, e.data.cols);
                    editor.insertContent(html);
                }
            });
        }
    });

    function buildTable(rows, cols) {
        var html = '<table style="border-collapse: collapse; width: 100%;">';
        html += '<tbody>';
        
        for (var x = 0; x < rows; x++) {
            html += '<tr>';
            for (var y = 0; y < cols; y++) {
                html += '<td style="border: 1px solid #ccc; padding: 8px;">&nbsp;</td>';
            }
            html += '</tr>';
        }

        html += '</tbody></table>';
        return html;
    }
});

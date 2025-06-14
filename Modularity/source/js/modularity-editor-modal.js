document.addEventListener("DOMContentLoaded", () => {
    (function($) {
        /**
         * Add new post callback
         */
        if (parent.Modularity.Editor.Thickbox.postAction == 'add' && modularity_post_action == '') {
            parent.Modularity.Editor.Thickbox.modulePostCreated(modularity_post_id);
        }
    
        if (parent.Modularity.Editor.Thickbox.postAction == 'edit-inline-saved') {
            parent.location.reload();
        }
    
        /**
         * Edit post callback
         */
        if (parent.Modularity.Editor.Thickbox.postAction == 'edit' && modularity_post_action == '') {
            jQuery(document).on('click', '#publish', (e) => {
                parent.Modularity.Editor.Thickbox.postAction = 'add';
            });
        }
    
        /**
         * Edit post callback
         */
        if (parent.Modularity.Editor.Thickbox.postAction == 'edit-inline-not-saved') {
            jQuery(document).on('click', '#publish', (e) => {
                parent.Modularity.Editor.Thickbox.postAction = 'edit-inline-saved';
            });
        }
    
        /**
         * Import post modifications and callback
         */
        if (parent.Modularity.Editor.Thickbox.postAction == 'import') {
            $('.check-column input[type="checkbox"]').remove();
            $('.wp-list-table').addClass('modularity-wp-list-table');
            $('tbody .check-column').addClass('modularity-import-column').append('<button class="button modularity-import-button" data-modularity-action="import">Import</button>');
            $('#posts-filter').append('<input type="hidden" name="is_thickbox" value="true">');
            $('.modularity-import-column label').addClass('screen-reader-text');
    
            $(document).on('click', '[data-modularity-action="import"]', (e) => {
                e.preventDefault();
    
                var postId = $(e.target).closest('tr').attr('id');
                postId = postId.split('-')[1];
    
                var module = parent.Modularity.Editor.Module.isEditingModule();
    
                var request = {
                    action: 'get_post',
                    id: postId
                };
    
                $('body').addClass('modularity-loader-takeover');
    
                $.post(ajaxurl, request, (response) => {
                    var data = {
                        post_id: response.ID,
                        title: response.post_title
                    };
    
                    parent.Modularity.Editor.Module.updateModule(module, data);
                    parent.Modularity.Editor.Autosave.save('form');
                    parent.Modularity.Prompt.Modal.close();
                }, 'json');
            });
        }
    
        /**
         * Import post modifications and callback
         */
        if (parent.Modularity.Editor.Thickbox.postAction == 'import-widget') {
            $('.check-column input[type="checkbox"]').remove();
            $('.wp-list-table').addClass('modularity-wp-list-table');
            $('tbody .check-column').addClass('modularity-import-column').append('<button class="button modularity-import-button" data-modularity-action="import">Import</button>');
    
            $(document).on('click', '[data-modularity-action="import"]', (e) => {
                e.preventDefault();
    
                var postId = $(e.target).closest('tr').attr('id');
                postId = postId.split('-')[1];
    
                var widget = parent.Modularity.Helpers.Widget.isEditingWidget();
    
                var request = {
                    action: 'get_post',
                    id: postId
                };
    
                $('body').addClass('modularity-loader-takeover');
    
                $.post(ajaxurl, request, (response) => {
                    var data = {
                        post_id: response.ID,
                        title: response.post_title
                    };
    
                    parent.Modularity.Helpers.Widget.updateWidget(widget, data);
                    parent.Modularity.Prompt.Modal.close();
                }, 'json');
            });
        }
    
    })(jQuery)
})

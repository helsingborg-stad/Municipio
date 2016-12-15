jQuery(document).ready(function ($) {

    /**
     * TAG MANAGER
     */
    $('[data-action="tag-manager-add-tag"]').on('click', function (e) {
        e.preventDefault();

        var tag = $('[data-tag-input]').val();
        var unit = $('[data-tag-unit-input]').val();
        var unit_name = $('[data-tag-unit-input] option[value="' + unit + '"]').text();
        if (tag.length === 0) {
            return;
        }

        $('.tag-manager-tags').append('\
            <div class="tag-manager-tag">\
                ' + tag + ' (' + unit_name + ')\
                <input type="hidden" name="tag-manager-tags[]" value="' + tag + '|' + unit + '">\
                <div class="tag-manager-actions">\
                    <button class="btn-plain tag-manager-delete-tag"><span class="dashicons dashicons-trash"></span></button>\
                </div>\
            </div>\
        ');

        $('[data-tag-input]').val('');
    });

    $(document).on('click', '.tag-manager-delete-tag', function (e) {
        e.preventDefault();
        $(this).parents('.tag-manager-tag').remove();
    });

    $(window).keydown(function (event) {
        if ($('[data-tag-input]').length === 0) {
            return;
        }

        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $('[data-tag-input]').keyup(function (e) {
        if (e.keyCode == 13)
        {
            e.preventDefault();
            $('[data-action="tag-manager-add-tag"]').trigger('click');
            return false;
        }
    });

    /**
     * USER SYNC
     */
    $('[data-action="users-sync-with-network"]').on('click', function () {
        var $parent = $(this).parent();
        var blogid = $(this).attr('data-blogid');
        $parent.find('.spinner').css('visibility', 'visible');

        $.post(ajaxurl, {action: 'sync_network_users', blog_id: blogid}, function (res) {
            $parent.find('.spinner').css('visibility', 'hidden');

            if (res.success === 'cron') {
                alert('A cronjob has been scheduled to run immediately');
                return;
            }

            if (res.success === true) {
                alert('Users synced with network');
                return;
            }

            alert('Woops, there was an error with the syncing process. Contact the developer(s).');
        });
    });

});



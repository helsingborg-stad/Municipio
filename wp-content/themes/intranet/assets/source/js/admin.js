jQuery(document).ready(function ($) {

    /**
     * TAG MANAGER
     */
    $('[data-action="tag-manager-add-tag"]').on('click', function (e) {
        e.preventDefault();

        var tag = $('[data-tag-input]').val();
        if (tag.length === 0) {
            return;
        }

        $('.tag-manager-tags').append('\
            <div class="tag-manager-tag">\
                ' + tag + '\
                <input type="hidden" name="tag-manager-tags[]" value="' + tag + '">\
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


});

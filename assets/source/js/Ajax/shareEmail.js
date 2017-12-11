Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.ShareEmail = (function ($) {

    function ShareEmail() {
        $('.social-share-email').on('submit', function (e) {
            e.preventDefault();
            var $target = $(this),
                data = new FormData(this);
                data.append('action', 'share_email');

            if (data.get('g-recaptcha-response') === '') {
                return false;
            }

            $target.find('input[type="submit"]').hide();
            $target.find('.modal-footer').append('<div class="loading"><div></div><div></div><div></div><div></div></div>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response, textStatus, jqXHR) {
                    if (response.success) {
                        $('.modal-footer', $target).html('<span class="notice success"><i class="pricon pricon-check"></i> ' + response.data + '</span>');

                    } else {
                        $('.modal-footer', $target).html('<span class="notice warning"><i class="pricon pricon-notice-warning"></i> ' + response.data + '</span>');
                    }
                },
                complete: function () {
                    setTimeout(function() {
                        location.hash = '';
                    }, 3000);
                }
            });

            return false;
        });
    }

    return new ShareEmail();

})(jQuery);

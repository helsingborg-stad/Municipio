Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.ShareEmail = (function ($) {

    function ShareEmail() {
        $(function(){
            this.handleEvents();
        }.bind(this));
    }

    /**
     * Handle events
     * @return {void}
     */
    ShareEmail.prototype.handleEvents = function () {
        $(document).on('submit', '.social-share-email', function (e) {
            e.preventDefault();
            this.share(e);

        }.bind(this));
    };

    ShareEmail.prototype.share = function(event) {
        var $target = $(event.target),
            data = new FormData(event.target);
            data.append('action', 'share_email');

        if (data.get('g-recaptcha-response') === '') {
            return false;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function() {
                $target.find('.modal-footer').prepend('<div class="loading"><div></div><div></div><div></div><div></div></div>');
                $target.find('.notice').hide();
            },
            success: function(response, textStatus, jqXHR) {
                if (response.success) {
                    $('.modal-footer', $target).prepend('<span class="notice success gutter gutter-margin gutter-vertical"><i class="pricon pricon-check"></i> ' + response.data + '</span>');

                    setTimeout(function() {
                        location.hash = '';
                        $target.find('.notice').hide();
                    }, 3000);
                } else {
                    $('.modal-footer', $target).prepend('<span class="notice warning gutter gutter-margin gutter-vertical"><i class="pricon pricon-notice-warning"></i> ' + response.data + '</span>');
                }
            },
            complete: function () {
                $target.find('.loading').hide();
            }
        });

        return false;
    };

    return new ShareEmail();

})(jQuery);

Intranet = Intranet || {};
Intranet.User = Intranet.User || {};

Intranet.User.Subscribe = (function ($) {

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Subscribe() {
        $('[data-subscribe]').on('click', function (e) {
            e.preventDefault();

            var buttonElement = $(e.target).closest('[data-subscribe]');
            var blogid = buttonElement.attr('data-subscribe');

            this.toggleSubscription(blogid, buttonElement);
        }.bind(this));
    }

    Subscribe.prototype.toggleSubscription = function (blogid, buttonElement) {
        var $allButtons = $('[data-subscribe="' + blogid + '"]');

        var postdata = {
            action: 'toggle_subscription',
            blog_id: blogid
        };

        $allButtons.html('<i class="spinner"></i>');

        $.post(ajaxurl, postdata, function (res) {
            if (res == 'subscribed') {
                $allButtons.html('<i class="pricon pricon-minus-o"></i> ' + municipioIntranet.unsubscribe);
            } else {
                $allButtons.html('<i class="pricon pricon-plus-o"></i> '  + municipioIntranet.subscribe);
            }
        });
    };

    return new Subscribe();

})(jQuery);

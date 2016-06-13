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
        var postdata = {
            action: 'toggle_subscription',
            blog_id: blogid
        };

        $.post(ajaxurl, postdata, function (res) {
            if (res == 'subscribed') {
                buttonElement.html('<i class="fa fa-minus-circle"></i> ' + municipioIntranet.unsubscribe);
            } else {
                buttonElement.html('<i class="fa fa-plus-circle"></i> '  + municipioIntranet.subscribe);
            }
        });
    };

    return new Subscribe();

})(jQuery);

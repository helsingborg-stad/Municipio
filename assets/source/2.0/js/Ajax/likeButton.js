var Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.LikeButton = (function($) {
    function Like() {
        this.init();
    }

    Like.prototype.init = function() {
        $('a.like-button').on(
            'click',
            function(e) {
                this.ajaxCall(e.target);
                return false;
            }.bind(this)
        );
    };

    Like.prototype.ajaxCall = function(likeButton) {
        var comment_id = $(likeButton).data('comment-id');
        var counter = $('span#like-count', likeButton);
        var button = $(likeButton);

        $.ajax({
            url: likeButtonData.ajax_url,
            type: 'post',
            data: {
                action: 'ajaxLikeMethod',
                comment_id: comment_id,
                nonce: likeButtonData.nonce,
            },
            beforeSend: function() {
                var likes = counter.html();

                if (button.hasClass('active')) {
                    likes--;
                    button.toggleClass('active');
                } else {
                    likes++;
                    button.toggleClass('active');
                }

                counter.html(likes);
            },
            success: function(response) {},
        });
    };

    return new Like();
})($);

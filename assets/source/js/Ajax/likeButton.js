

Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.LikeButton = (function ($) {

    function Like() {
        this.name = 'nikolas';
        this.init();
    }

    Like.prototype.init = function() {
        $('a.like-button').on('click', function(e) {

            this.ajaxCall(e.target);

            return false;

        }.bind(this));
    }

    Like.prototype.ajaxCall = function(likeButton) {
        var post_id = $(likeButton).data('post-id');
        var counter = $('span#like-count', likeButton);
        var button = $(likeButton);

        $.ajax({
            url : likeButtonData.ajax_url,
            type : 'post',
            data : {
                action : 'likeButton',
                post_id : post_id,
                // send the nonce along with the request
                nonce : likeButtonData.nonce
            },
            beforeSend: function() {
                var likes = counter.html();

                if(button.hasClass('active')) {
                    likes--;
                    button.toggleClass("active");
                }
                else {
                    likes++;
                    button.toggleClass("active");
                }

                counter.html( likes );
            },
            success : function( response ) {
                //counter.html( response );
            }
        });

    };

    return new Like();

})($);

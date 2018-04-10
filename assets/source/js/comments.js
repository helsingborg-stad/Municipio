Muncipio = Muncipio || {};
Muncipio.Post = Muncipio.Post || {};

Muncipio.Post.Comments = (function ($) {

    function Comments() {
        $(function() {
            this.handleEvents();
        }.bind(this));
    }

    /**
     * Handle events
     * @return {void}
     */
    Comments.prototype.handleEvents = function () {
        $(document).on('click', '#delete-comment', function (e) {
            e.preventDefault();
            if (window.confirm(MunicipioLang.messages.deleteComment)) {
                this.deleteComment(e);
            }
        }.bind(this));
    };

    Comments.prototype.deleteComment = function(event) {
        var $target = $(event.currentTarget),
            commentId = $target.data('comment-id'),
            nonce = $target.data('comment-nonce');

        $.ajax({
            url: ajaxurl,
            type: 'post',
            context: this,
            dataType: 'json',
            data: {
                action      : 'remove_comment',
                id          : commentId,
                _ajax_nonce : nonce
            },
            beforeSend : function(response) {
                // Do expected behavior
                $target.closest('li.answer, li.comment').fadeOut();
            },
            success : function(response) {
                if (!response.success) {
                    // Undo front end deletion
                    this.showError($target);
                }
            },
            error : function(e) {
                this.showError($target);
            }
        });
    };

    Comments.prototype.showError = function(target) {
        target.closest('li.answer, li.comment').fadeIn()
            .find('.comment-body:first').append('<small class="text-danger">' + MunicipioLang.messages.onError + '</small>')
                .find('.text-danger').delay(4000).fadeOut();
    };

    return new Comments();

})(jQuery);

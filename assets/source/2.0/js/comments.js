var Muncipio = Muncipio || {};
Muncipio.Post = Muncipio.Post || {};

Muncipio.Post.Comments = (function($) {
    function Comments() {
        $(
            function() {
                this.handleEvents();
            }.bind(this)
        );
    }

    /**
     * Handle events
     * @return {void}
     */
    Comments.prototype.handleEvents = function() {
        $(document).on(
            'click',
            '#edit-comment',
            function(e) {
                e.preventDefault();
                this.displayEditForm(e);
            }.bind(this)
        );

        $(document).on(
            'submit',
            '#commentupdate',
            function(e) {
                e.preventDefault();
                this.udpateComment(e);
            }.bind(this)
        );

        $(document).on(
            'click',
            '#delete-comment',
            function(e) {
                e.preventDefault();
                if (window.confirm(MunicipioLang.messages.deleteComment)) {
                    this.deleteComment(e);
                }
            }.bind(this)
        );

        $(document).on(
            'click',
            '.cancel-update-comment',
            function(e) {
                e.preventDefault();
                this.cleanUp();
            }.bind(this)
        );

        $(document).on(
            'click',
            '.comment-reply-link',
            function(e) {
                this.cleanUp();
            }.bind(this)
        );
    };

    Comments.prototype.udpateComment = function(event) {
        var $target = $(event.target)
                .closest('.comment-body')
                .find('.comment-content'),
            data = new FormData(event.target),
            oldComment = $target.html();
        data.append('action', 'update_comment');

        $.ajax({
            url: ajaxurl,
            type: 'post',
            context: this,
            processData: false,
            contentType: false,
            data: data,
            dataType: 'json',
            beforeSend: function() {
                // Do expected behavior
                $target.html(data.get('comment'));
                this.cleanUp();
            },
            success: function(response) {
                if (!response.success) {
                    // Undo front end update
                    $target.html(oldComment);
                    this.showError($target);
                }
            },
            error: function(jqXHR, textStatus) {
                $target.html(oldComment);
                this.showError($target);
            },
        });
    };

    Comments.prototype.displayEditForm = function(event) {
        var commentId = $(event.currentTarget).data('comment-id'),
            postId = $(event.currentTarget).data('post-id'),
            $target = $(
                '.comment-body',
                '#answer-' + commentId + ', #comment-' + commentId
            ).first();

        this.cleanUp();
        $('.comment-content, .comment-footer', $target).hide();
        $target.append(
            '<div class="loading gutter gutter-top gutter-margin"><div></div><div></div><div></div><div></div></div>'
        );

        $.when(this.getCommentForm(commentId, postId)).then(function(response) {
            if (response.success) {
                $target.append(response.data);
                $('.loading', $target).remove();

                // Re init tinyMce if its used
                if ($('.tinymce-editor').length) {
                    tinymce.EditorManager.execCommand('mceRemoveEditor', true, 'comment-edit');
                    tinymce.EditorManager.execCommand('mceAddEditor', true, 'comment-edit');
                }
            } else {
                this.cleanUp();
                this.showError($target);
            }
        });
    };

    Comments.prototype.getCommentForm = function(commentId, postId) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            context: this,
            data: {
                action: 'get_comment_form',
                commentId: commentId,
                postId: postId,
            },
        });
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
                action: 'remove_comment',
                id: commentId,
                nonce: nonce,
            },
            beforeSend: function(response) {
                // Do expected behavior
                $target.closest('li.answer, li.comment').fadeOut('fast');
            },
            success: function(response) {
                if (!response.success) {
                    // Undo front end deletion
                    this.showError($target);
                }
            },
            error: function(jqXHR, textStatus) {
                this.showError($target);
            },
        });
    };

    Comments.prototype.cleanUp = function(event) {
        $('.comment-update').remove();
        $('.loading', '.comment-body').remove();
        $('.dropdown-menu').hide();
        $('.comment-content, .comment-footer').fadeIn('fast');
    };

    Comments.prototype.showError = function(target) {
        target
            .closest('li.answer, li.comment')
            .fadeIn('fast')
            .find('.comment-body:first')
            .append('<small class="text-danger">' + MunicipioLang.messages.onError + '</small>')
            .find('.text-danger')
            .delay(4000)
            .fadeOut('fast');
    };

    return new Comments();
})(jQuery);

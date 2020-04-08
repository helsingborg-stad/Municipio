/**
 * Municipio Comments
 */
export default class Comments {
    
    /**
     * Init
     */
    constructor() {
        this.initFancyCancelReply();
        this.cancelReplyOnClick();
        this.adminDeleteComment();
        this.likeComment();
    }
    
    /**
     * Show reply buttons
     */
    removeClass() {
        const replyLink = document.querySelectorAll('.comment-reply-link');
        for (let int = 0; int < replyLink.length; int++) {
            if (replyLink[int].classList.contains('u-display--none')) {
                replyLink[int].classList.remove('u-display--none');
            }
        }
    }
    
    /**
     * Cancel Comment reply
     */
    cancelReplyOnClick() {
        const cancelOnClick = document.getElementById('cancel-comment-reply-link');
        cancelOnClick.addEventListener('click', this.removeClass);
    }
    
    /**
     * Init fancy Cancel Reply
     */
    initFancyCancelReply() {
        const commentReplyLink = document.querySelectorAll('.comment-reply-link');
        for (let int = 0; int < commentReplyLink.length; int++) {
            commentReplyLink[int].addEventListener('click', this.makeLinkFancy);
        }
    }
    
    /**
     * Add fancy button styles to link
     */
    makeLinkFancy() {
        const classList = ['c-button', 'u-float--right', 'c-button__basic', 'basic--secondary', 'c-button--md'];
        document.getElementById('cancel-comment-reply-link').classList.add(...classList);
    }
    
    /**
     * Delete Comment eventListner
     */
    adminDeleteComment() {
        const deleteButton = document.querySelectorAll('.delete-comment');
        for (let int = 0; int < deleteButton.length; int++) {
            deleteButton[int].addEventListener('click', function (element) {
                
                let commentId = element.getAttribute('comment-id'),
                    nonce = element.getAttribute('comment-nonce');
                
                fetch(ajaxurl, {
                    method: "POST",
                    context: self,
                    body: {
                        action: 'remove_comment',
                        id: commentId,
                        nonce: nonce,
                    }
                }).then(response => {
                    if (!response.success) {
                        // Undo front end deletion
                        //this.showError($target);
                    }
                });
            });
        }
    }
    
    /**
     * Like comment
     */
    likeComment() {
        const likeButton = document.querySelectorAll('.like-button');
        for (let int = 0; int < likeButton.length; int++) {
            likeButton[int].addEventListener('click', function () {
                
                const commentId = this.getAttribute('data-commentid');
                const commentCounter = document.getElementById('comment-likes-'+commentId);
    
                const formQueries = new FormData();
                formQueries.append('action', 'ajaxLikeMethod');
                formQueries.append('id', commentId);
                
                const params = new URLSearchParams(formQueries);
                
                fetch(ajaxurl, {
                    method: "POST",
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Cache-Control': 'no-cache',
                    },
                    context: self,
                    body: params
                }).then(response => {
    
                    let likes = commentCounter.getAttribute('data-likes');
                    
                    if (this.classList.contains('u-disabled')) {
                        likes--;
                        this.classList.remove('u-disabled');
                    } else {
                        likes++;
                        this.querySelector('.c-button__label span').innerHTML = HbgPrimeArgs.comments.dislike;
                        this.setAttribute('data-like-unset','true');
                    }
                    
                    commentCounter.innerHTML = likes;
                    commentCounter.setAttribute('data-likes', likes);
                });
            });
        }
    }
    
}
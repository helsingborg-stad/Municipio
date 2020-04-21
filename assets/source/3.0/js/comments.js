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
        this.likeComment();
        this.changeTextareaHeight();
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
     * Listen to commentform typing
     */
    changeTextareaHeight() {
        const self = this;
        document.getElementById('comment').addEventListener('keydown', function (event) {
            self.textareaHeight(event);
        }, false);
        document.getElementById('comment').addEventListener('keyup', function (event) {
            self.textareaHeight(event);
        }, false);
    }
    
    /**
     * Change height of textarea
     * @param event
     */
    textareaHeight(event){
        if (event.keyCode !== 8 && event.keyCode !== 46 || event.keyCode === 8 || event.keyCode === 46) {
            let text = document.getElementById('comment').value + String.fromCharCode(event.keyCode);
            document.getElementById('comment').rows = text.split(/\r\n|\r|\n/).length;
            console.log('SMAXK');
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
                formQueries.append('comment_id', commentId);
                formQueries.append('nonce', likeButtonData.nonce);
        
                const params = new URLSearchParams(formQueries);
                
                fetch(likeButtonData.ajax_url, {
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
                    if (this.classList.contains('active')) {
                        
                        likes--;
                        this.classList.remove('active');
                        
                    } else {
                        
                        likes++;
                        this.classList.add('active');
                    }
                    
                    commentCounter.innerHTML = likes;
                    commentCounter.setAttribute('data-likes', likes);
                });
            });
        }
    }
    
}
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
    }
    
    /**
     * Show reply buttons
     */
    removeClass(){
        const replyLink = document.querySelectorAll( '.comment-reply-link' );
        for(let int = 0; int < replyLink.length; int++){
            if (replyLink[int].classList.contains('u-display--none')) {
                replyLink[int].classList.remove('u-display--none');
            }
        }
    }
    
    /**
     * Cancel Comment reply
     */
    cancelReplyOnClick () {
        const cancelOnClick = document.getElementById( 'cancel-comment-reply-link' );
        cancelOnClick.addEventListener( 'click', this.removeClass );
    }
    
    /**
     * Init fancy Cancel Reply
     */
    initFancyCancelReply(){
        const commentReplyLink = document.querySelectorAll( '.comment-reply-link' );
        for(let int = 0; int < commentReplyLink.length; int++) {
            commentReplyLink[int].addEventListener('click', this.makeLinkFancy);
        }
    }
    
    /**
     * Add fancy button styles to link
     */
    makeLinkFancy(){
        const classList = ['c-button', 'u-float--right', 'c-button__basic', 'basic--secondary', 'c-button--md'];
        document.getElementById( 'cancel-comment-reply-link' ).classList.add(...classList);
    }
    
    
    
}
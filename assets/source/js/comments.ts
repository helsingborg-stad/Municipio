/**
 * Comments Manager
 */
export class Comments {
    /**
     * Initialize the comments functionality.
     */
    public static initializeComments(): void {
      this.initFancyCancelReply();
      this.initCancelReplyOnClick();
      this.initLikeComment();
      this.initTextareaHeightHandler();
    }
  
    /**
     * Initialize fancy cancel reply functionality.
     */
    private static initFancyCancelReply(): void {
      const replyLinks = document.querySelectorAll('.comment-reply-link') as NodeListOf<HTMLElement>;
      replyLinks.forEach((replyLink) => {
        replyLink.addEventListener('click', Comments.applyFancyStyleToCancelLink);
      });
    }
  
    /**
     * Apply fancy button styles to the cancel reply link.
     */
    private static applyFancyStyleToCancelLink(): void {
      const cancelLink = document.getElementById('cancel-comment-reply-link');
      if (cancelLink) {
        const classList = [
          'c-button',
          'u-float--right',
          'c-button__basic',
          'basic--secondary',
          'c-button--md',
        ];
        cancelLink.classList.add(...classList);
      }
    }
  
    /**
     * Initialize cancel reply functionality on click.
     */
    private static initCancelReplyOnClick(): void {
      const cancelLink = document.getElementById('cancel-comment-reply-link');
      if (cancelLink) {
        cancelLink.addEventListener('click', Comments.showReplyButtons);
      }
    }
  
    /**
     * Show reply buttons by removing the hidden class.
     */
    private static showReplyButtons(): void {
      const replyLinks = document.querySelectorAll('.comment-reply-link') as NodeListOf<HTMLElement>;
      replyLinks.forEach((replyLink) => {
        replyLink.classList.remove('u-display--none');
      });
    }
  
    /**
     * Initialize textarea height adjustment handlers.
     */
    private static initTextareaHeightHandler(): void {
      const commentTextarea = document.getElementById('comment') as HTMLTextAreaElement | null;
      if (commentTextarea) {
        commentTextarea.addEventListener('keydown', Comments.adjustTextareaHeight);
        commentTextarea.addEventListener('keyup', Comments.adjustTextareaHeight);
      }
    }
  
    /**
     * Adjust the height of the textarea dynamically.
     * @param event - Keyboard event triggered on the textarea.
     */
    private static adjustTextareaHeight(event: KeyboardEvent): void {
      const textarea = event.target as HTMLTextAreaElement;
      if (textarea) {
        const lines = textarea.value.split(/\r\n|\r|\n/).length;
        textarea.rows = lines;
      }
    }
  
    /**
     * Initialize like comment functionality.
     */
    private static initLikeComment(): void {
      const likeButtons = document.querySelectorAll('.like-button') as NodeListOf<HTMLElement>;
      likeButtons.forEach((likeButton) => {
        likeButton.addEventListener('click', Comments.handleLikeButtonClick);
      });
    }
  
    /**
     * Handle like button click.
     * @param event - The click event on the like button.
     */
    private static handleLikeButtonClick(event: Event): void {
      const button = event.currentTarget as HTMLElement;
      if (!button) return;
  
      const commentId = button.getAttribute('data-commentid');
      const commentCounter = document.getElementById(`comment-likes-${commentId}`);
      if (!commentId || !commentCounter) return;
  
      const formData = new FormData();
      formData.append('action', 'ajaxLikeMethod');
      formData.append('comment_id', commentId);
      formData.append('nonce', (window as any).likeButtonData.nonce);
  
      const params = new URLSearchParams(formData as any);
  
      fetch((window as any).likeButtonData.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Cache-Control': 'no-cache',
        },
        body: params.toString(),
      })
        .then((response) => response.json())
        .then(() => {
          let likes = parseInt(commentCounter.getAttribute('data-likes') || '0', 10);
          if (button.classList.contains('active')) {
            likes--;
            button.classList.remove('active');
          } else {
            likes++;
            button.classList.add('active');
          }
  
          commentCounter.textContent = likes.toString();
          commentCounter.setAttribute('data-likes', likes.toString());
        })
        .catch((error) => console.error('Error liking comment:', error));
    }
  }
  
  /**
   * Initialize the comments manager.
   */
  export function initializeComments(): void {
    document.addEventListener('DOMContentLoaded', () => {
      Comments.initializeComments();
    });
  }
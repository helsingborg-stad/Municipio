class Feedback implements FeedbackInterface {
    private liked: boolean | null = null;
    constructor(
        private chatInstance: any,
        private messageInstance: any,
        private likeButton: HTMLElement,
        private dislikeButton: HTMLElement
    ) {
        this.liked = this.getLikeStatus();
        this.updateFeedbackClasses();
        this.setListeners();
    }

    private setListeners(): void {
        this.likeButton.addEventListener("click", () => {
            this.setLikeStatus(this.liked === true ? null : true);
        });

        this.dislikeButton.addEventListener("click", () => {
            this.setLikeStatus(this.liked === false ? null : false);
        });
        
    }

    private setLikeStatus(liked: boolean | null): void {
        if (liked === this.liked) {
            return;
        }

        this.liked = liked;
        const data = this.messageInstance.getData() ?? {};
        data.liked = this.liked;
        this.messageInstance.setData(data);
        this.updateFeedbackClasses();
        this.chatInstance.updateMessage(this.messageInstance);
    }

    private updateFeedbackClasses(): void {
        const messageContainer = this.messageInstance.getMessageContainer();
        messageContainer.classList.remove("is-liked", "is-disliked");

        if (this.liked === true) {
            messageContainer.classList.add("is-liked");
            return;
        }

        if (this.liked === false) {
            messageContainer.classList.add("is-disliked");
        }
    }

    private getLikeStatus(): boolean | null {
        const data = this.messageInstance.getData() ?? {};

        if (data.liked === undefined || data.liked === null) {
            return null;
        }

        return data.liked === true;
    }
}

export default Feedback;
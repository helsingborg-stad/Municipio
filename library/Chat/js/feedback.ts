import FeedbackApi from "./feedbackApi";

class Feedback implements FeedbackInterface {
    private liked: boolean | null = null;
    private materialSymbolsFilledClass = 'material-symbols--filled';

    constructor(
        private chatInstance: any,
        private messageInstance: any,
        private likeButton: HTMLElement,
        private dislikeButton: HTMLElement,
        private feedbackApi: FeedbackApi
    ) {
        this.liked = this.getLikeStatus();
        this.updateFeedbackClasses();
        this.updateButtonClasses();
        this.setListeners();
    }

    private setListeners(): void {
        this.likeButton.addEventListener("click", () => {
            const status = this.liked === true ? null : true;
            this.setLikeStatus(status);
            this.updateButtonClasses();
        });

        this.likeButton.addEventListener("mouseenter", () => {
            this.likeButton.classList.add(this.materialSymbolsFilledClass);
        });

        this.likeButton.addEventListener("mouseleave", () => {
            this.updateButtonClasses();
        });

        this.dislikeButton.addEventListener("click", () => {
            const status = this.liked === false ? null : false;
            this.setLikeStatus(status);
            this.updateButtonClasses();
        });

        this.dislikeButton.addEventListener("mouseenter", () => {
            this.dislikeButton.classList.add(this.materialSymbolsFilledClass);
        });

        this.dislikeButton.addEventListener("mouseleave", () => {
            this.updateButtonClasses();
        });
    }

    private updateButtonClasses(): void {
        this.likeButton.classList.toggle(this.materialSymbolsFilledClass, this.liked === true);
        this.dislikeButton.classList.toggle(this.materialSymbolsFilledClass, this.liked === false);
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

        if (liked === true) {
            this.feedbackApi.postStat('like');
        } else if (liked === false) {
            this.feedbackApi.postStat('dislike');
        }
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
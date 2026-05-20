import Feedback from "./feedback";

class FeedbackFactory implements FeedbackFactoryInterface {
    constructor(private chatInstance: any, private feedbackTemplate: HTMLTemplateElement|null) {}

    public create(messageInstance: any): FeedbackInterface {
        const feedbackFragment = this.feedbackTemplate!.content.cloneNode(true) as DocumentFragment;
        const like = feedbackFragment.querySelector("[data-js-chat-message-like-button]") as HTMLElement;
        const dislike = feedbackFragment.querySelector("[data-js-chat-message-dislike-button]") as HTMLElement;

        const feedback = new Feedback(this.chatInstance, messageInstance, like, dislike);
        messageInstance.getMessageContainer().appendChild(feedbackFragment);
        return feedback;
    }
}

export default FeedbackFactory;
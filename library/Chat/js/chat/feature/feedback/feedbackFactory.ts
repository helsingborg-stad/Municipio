import Feedback from "./feedback";
import FeedbackApi from "./feedbackApi";

class FeedbackFactory implements FeedbackFactoryInterface {
	constructor(
		private chatInstance: any,
		private feedbackTemplate: HTMLTemplateElement|null,
		private feedbackApi: FeedbackApi
	) {}

	/**
	 * Adds like/dislike buttons to a message. Returns null (and adds nothing) when the
	 * chat has no feedback template or the template is missing its buttons.
	 */
	public create(messageInstance: any): FeedbackInterface | null {
		if (!this.feedbackTemplate) {
			return null;
		}

		const feedbackFragment = this.feedbackTemplate.content.cloneNode(true) as DocumentFragment;
		const like = feedbackFragment.querySelector("[data-js-chat-message-like-button]") as HTMLElement | null;
		const dislike = feedbackFragment.querySelector("[data-js-chat-message-dislike-button]") as HTMLElement | null;

		if (!like || !dislike) {
			return null;
		}

		const feedback = new Feedback(this.chatInstance, messageInstance, like, dislike, this.feedbackApi);
		messageInstance.getMessageContainer().appendChild(feedbackFragment);
		return feedback;
	}
}

export default FeedbackFactory;

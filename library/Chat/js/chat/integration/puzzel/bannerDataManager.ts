import { getLocalizedString } from "../../helper/translations";

/** Builds the localized text shown in the chat banner. */
class BannerDataManager implements BannerDataManagerInterface {
    public constructor(private chatTemplate: HTMLTemplateElement | null = null) {
    }

    public getQueueBannerContent(position: number): Node {
        const queuePositionLabel = getLocalizedString("queuePosition", "Queue position");

        return document.createTextNode(`${queuePositionLabel}: ${position}`);
    }

    public getAgentLeftContent(): Node {
        const text = document.createTextNode(
            getLocalizedString(
                "agentLeftChatInactive",
                "The agent has left and the chat is now inactive",
            )
        );

        const node = this.buildNode(text, this.getNewChatButton());
        console.log(node)
        return node;
    }

    public getErrorBannerContent(): Node {
        const text = getLocalizedString("errorMessage", "Something went wrong, please try again later");
        return document.createTextNode(text);
    }

    private buildNode(...nodes: Node[]): DocumentFragment {
        const fragment = document.createDocumentFragment();

        nodes.forEach(node => {
            fragment.appendChild(node.cloneNode(true));
        });

        return fragment;
    }

    public getNewChatButton(): Node {
        const button = this.chatTemplate?.content.querySelector("[data-js-new-chat]");

        if (!button) {
            return document.createTextNode("");
        }

        return button.cloneNode(true);
    }
}

export default BannerDataManager;

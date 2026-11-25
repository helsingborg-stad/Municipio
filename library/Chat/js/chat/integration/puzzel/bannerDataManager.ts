import { getLocalizedString } from "../../helper/translations";

/** Builds the localized text shown in the chat banner. */
class BannerDataManager implements BannerDataManagerInterface {
    public getQueueBannerContent(position: number): string {
        const queuePositionLabel = getLocalizedString("queuePosition", "Queue position");

        return `${queuePositionLabel}: ${position}`;
    }

    public getChatInactiveBannerContent(): string {
        return getLocalizedString(
            "agentLeftChatInactive",
            "The agent has left and the chat is now inactive",
        );
    }

    public getErrorBannerContent(): string {
        return getLocalizedString("errorMessage", "Something went wrong, please try again later");
    }
}

export default BannerDataManager;

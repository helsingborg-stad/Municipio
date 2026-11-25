import { getLocalizedString } from "../../helper/translations";

/** Builds the localized info text added to the chat when participants join or leave. */
class InfoDataManager implements InfoDataManagerInterface {
    public getAgentJoinedInfoContent(): string {
        return getLocalizedString("agentJoinedChat", "Agent has joined the chat");
    }

    public getAgentLeftInfoContent(): string {
        return getLocalizedString("agentLeftChat", "Agent has left the chat");
    }

    public getUserLeftInfoContent(): string {
        return getLocalizedString("userLeftChat", "User has left the chat");
    }

    /** Returns the info text for a Puzzel event, or null if the event shows no info. */
    public getInfoContentFromType(type: string, senderRole: string = "agent"): string | null {
        switch (type) {
            case "participantJoinEvent":
                return senderRole === "agent" ? this.getAgentJoinedInfoContent() : null;
            case "participantLeaveEvent":
                return senderRole === "agent" ? this.getAgentLeftInfoContent() : this.getUserLeftInfoContent();
            default:
                return null;
        }
    }
}

export default InfoDataManager;

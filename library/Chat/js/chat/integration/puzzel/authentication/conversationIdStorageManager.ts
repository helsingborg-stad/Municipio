import { SESSION_ID_KEY } from "../../../enums";

class ConversationIdStorageManager implements ConversationIdStorageManagerInterface {
    private conversation: RouteConversationResponse | null = null;

    public getConversationId(): string | null {
        return this.getConversation()?.conversationId ?? null;
    }

    public getConversation(): RouteConversationResponse | null {
        const storedData = window.localStorage.getItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS
        );

        if (!storedData) {
            return null;
        }

        const data = JSON.parse(storedData);

        return data.puzzel ?? null;
    }

    public setConversationId(conversationResponse: RouteConversationResponse): void {

        const storedData = window.localStorage.getItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS
        );

        const data = storedData
            ? JSON.parse(storedData)
            : {};

        data.puzzel = {
            ...data.puzzel,
            conversationId: conversationResponse.conversationId,
            iqSessionId: conversationResponse.iqSessionId,
            requestId: conversationResponse.requestId,
            result: conversationResponse.result,
        };

        window.localStorage.setItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS,
            JSON.stringify(data)
        );
    }

}

export default ConversationIdStorageManager;
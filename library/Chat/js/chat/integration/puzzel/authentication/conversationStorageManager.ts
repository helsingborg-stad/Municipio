import { SESSION_ID_KEY } from "../../../enums";

class ConversationStorageManager implements ConversationStorageManagerInterface {
    public getConversationId(): string | null {
        return this.getLocalStorage()?.conversationId ?? null;
    }

    public getAccessToken(): string | null {
        return this.getLocalStorage()?.accessToken ?? null;
    }

    public getLocalStorage(): PuzzelLocalStorageData | null {
        const storedData = window.localStorage.getItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS
        );

        if (!storedData) {
            return null;
        }

        const data = JSON.parse(storedData);

        return data.puzzel ?? null;
    }

    public setLocalStorage(conversationResponse: RouteConversationResponse, accessToken: string): void {
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
            accessToken: accessToken
        };

        window.localStorage.setItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS,
            JSON.stringify(data)
        );
    }

}

export default ConversationStorageManager;
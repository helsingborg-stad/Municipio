import { SESSION_ID_KEY } from "../../../enums";

class ConversationStorageManager implements ConversationStorageManagerInterface {
    public getConversationId(): string | null {
        return this.getConversationData()?.conversationId ?? null;
    }

    public getAccessToken(): string | null {
        return this.getAuthenticationData()?.access_token ?? null;
    }

    public getConversationData(): RouteConversationResponse | null {
        return this.getLocalStorage()?.conversationData ?? null;
    }

    public getAuthenticationData(): AuthenticationResponse | null {
        return this.getLocalStorage()?.authenticationData ?? null;
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

    public setLocalStorage(conversationResponse: RouteConversationResponse, authentication: AuthenticationResponse): void {
        const storedData = window.localStorage.getItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS
        );

        const data = storedData
            ? JSON.parse(storedData)
            : {};

        data.puzzel = {
            ...data.puzzel,
            conversationData: conversationResponse,
            authenticationData: authentication
        };

        window.localStorage.setItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS,
            JSON.stringify(data)
        );
    }

}

export default ConversationStorageManager;
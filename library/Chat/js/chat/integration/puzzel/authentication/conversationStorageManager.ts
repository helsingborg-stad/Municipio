import { SESSION_ID_KEY } from "../../../enums";

class ConversationStorageManager implements ConversationStorageManagerInterface {
    private localStorageData: PuzzelLocalStorageData | null = null;

    public getConversationId(): string | null {
        return this.getConversationData()?.conversationId ?? null;
    }

    public getAccessToken(): string | null {
        return this.getAuthenticationData()?.access_token ?? null;
    }

    public getConversationData(): RouteConversationResponse | null {
        return this.getLocalStorage()?.conversationData ?? null;
    }

    public getAuthenticationData(): AuthenticationData | null {
        return this.getLocalStorage()?.authenticationData ?? null;
    }

    public clearLocalStorage(): void {
        this.localStorageData = null;
        const storedData = window.localStorage.getItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS
        );

        if (!storedData) {
            return;
        }

        const data = JSON.parse(storedData);
        delete data.puzzel;
        window.localStorage.setItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS,
            JSON.stringify(data)
        );
    }

    public getLocalStorage(): PuzzelLocalStorageData | null {
        if (this.localStorageData) {
            return this.localStorageData;
        }

        const storedData = window.localStorage.getItem(
            SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS
        );

        if (!storedData) {
            return null;
        }

        const data = JSON.parse(storedData);
        this.localStorageData = data.puzzel ?? null;
        return this.localStorageData;
    }

    public setLocalStorage(conversationResponse: RouteConversationResponse, authentication: AuthenticationData): void {
        this.localStorageData = null;

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